<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InAppCommunication\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\InAppCommunication\Actions\AddUserToConversation;
use AdvisingApp\InAppCommunication\Actions\CreateTwilioConversation;
use AdvisingApp\InAppCommunication\Actions\DeleteTwilioConversation;
use AdvisingApp\InAppCommunication\Actions\DemoteUserFromChannelManager;
use AdvisingApp\InAppCommunication\Actions\PromoteUserToChannelManager;
use AdvisingApp\InAppCommunication\Actions\RemoveUserFromConversation;
use AdvisingApp\InAppCommunication\Actions\TogglePinConversation;
use AdvisingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AdvisingApp\InAppCommunication\Enums\ConversationType;
use AdvisingApp\InAppCommunication\Events\ConversationMessageSent;
use AdvisingApp\InAppCommunication\Jobs\NotifyConversationParticipants;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;
use AdvisingApp\IntegrationTwilio\Actions\GetTwilioApiKey;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use App\Enums\Feature;
use App\Enums\Integration;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Rest\Client;

/**
 * @property Collection $conversations
 */
class UserChat extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    #[Url(as: 'conversation')]
    public ?string $conversationId = null;

    public ?TwilioConversation $conversation = null;

    public array $conversationActiveUsers = [];

    protected static ?string $navigationGroup = 'Premium Features';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'in-app-communication::filament.pages.user-chat';

    protected static ?string $title = 'Realtime Chat';

    public function getView(): string
    {
        if (Integration::Twilio->isOff()) {
            return 'filament.pages.integration-problem';
        }

        return parent::getView();
    }

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        return Gate::check(Feature::RealtimeChat->getGateName())
            && $user->can('realtime_chat.view-any') && $user->can('realtime_chat.*.view');
    }

    public function mount(): void
    {
        $this->selectConversation(
            $this->conversationId
            ? TwilioConversation::find($this->conversationId)
            : null
        );
    }

    #[Computed]
    public function conversations(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->conversations()
            ->with(['participants' => fn (BelongsToMany $query) => $query->whereKeyNot($user->getKey())])
            ->addSelect(['other_participant_name' => User::query()
                ->select('name')
                ->join('twilio_conversation_user', 'twilio_conversation_user.user_id', '=', 'users.id')
                ->whereColumn('twilio_conversation_user.conversation_sid', 'twilio_conversations.sid')
                ->whereKeyNot($user->getKey())
                ->limit(1),
            ])
            ->orderByDesc('is_pinned')
            ->orderBy('channel_name')
            ->orderBy('other_participant_name')
            ->get();
    }

    public function togglePinChannelAction(): Action
    {
        return Action::make('togglePinChannel')
            ->iconButton()
            ->size('sm')
            ->action(function (array $arguments) {
                /** @var User $user */
                $user = auth()->user();

                /** @var TwilioConversation $conversation */
                $conversation = $user->conversations()->find($arguments['id']);

                app(TogglePinConversation::class)(user: $user, conversation: $conversation);
            });
    }

    public function newUserToUserChatAction(): Action
    {
        $usersQuery = User::query()
            ->where('id', '!=', auth()->id())
            ->whereDoesntHave(
                'conversations',
                fn (Builder $query) => $query
                    ->where('type', ConversationType::UserToUser)
                    ->whereHas(
                        'participants',
                        fn (Builder $query) => $query->where('user_id', auth()->id())
                    )
            );

        return Action::make('newUserToUserChat')
            ->label('New Direct Message')
            ->modalWidth('sm')
            ->modalSubmitActionLabel('Start chat')
            ->form([
                Select::make('user')
                    ->label('Pick a user to chat with')
                    ->options(
                        fn (): array => $usersQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $usersQuery
                            ->where('name', 'ilike', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelUsing(
                        fn ($value) => $usersQuery->find($value)->getKey(),
                    )
                    ->searchable(),
            ])
            ->action(function (CreateTwilioConversation $createTwilioConversation, array $data) {
                $conversation = $createTwilioConversation(
                    type: ConversationType::UserToUser,
                    users: [
                        auth()->user(),
                        User::findOrFail($data['user']),
                    ],
                );

                if ($conversation) {
                    $this->selectConversation($conversation);

                    Notification::make()
                        ->title('Chat created.')
                        ->success()
                        ->send();
                }
            });
    }

    public function newChannelAction(): Action
    {
        $usersQuery = User::query()
            ->where('id', '!=', auth()->id());

        return Action::make('newChannel')
            ->label('New Channel')
            ->modalWidth('sm')
            ->modalSubmitActionLabel('Create channel')
            ->form([
                TextInput::make('name')
                    ->label('Channel name')
                    ->required(),
                Select::make('users')
                    ->label('Pick users to invite')
                    ->multiple()
                    ->options(
                        fn (): array => $usersQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $usersQuery
                            ->where('name', 'ilike', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelsUsing(
                        fn (array $values): array => $usersQuery
                            ->whereKey($values)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->searchable(),
                Checkbox::make('is_private')
                    ->label('Invite only')
                    ->default(true)
                    ->helperText('If not checked, the channel will be public and anyone can join.'),
            ])
            ->action(function (CreateTwilioConversation $createTwilioConversation, array $data) {
                $conversation = $createTwilioConversation(
                    type: ConversationType::Channel,
                    users: [
                        auth()->user(),
                        ...User::find($data['users'])->all(),
                    ],
                    channelName: $data['name'],
                    isPrivateChannel: $data['is_private'],
                );

                if ($conversation) {
                    $this->selectConversation($conversation);

                    Notification::make()
                        ->title('Channel created.')
                        ->success()
                        ->send();
                }
            });
    }

    public function editChannelAction(): Action
    {
        $usersQuery = $this->conversation
            ->participants()
            ->whereNot('id', auth()->id());

        return Action::make('editChannel')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalWidth('sm')
            ->form([
                TextInput::make('name')
                    ->autocomplete(false)
                    ->formatStateUsing(fn () => $this->conversation?->channel_name)
                    ->required(),
                Checkbox::make('is_private')
                    ->label('Invite only')
                    ->helperText('If not checked, the channel will be public and anyone can join.')
                    ->formatStateUsing(fn () => $this->conversation?->is_private_channel),
                Select::make('managers')
                    ->label('Channel managers')
                    ->multiple()
                    ->options(
                        fn (): array => $usersQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $usersQuery
                            ->where('name', 'ilike', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelsUsing(
                        fn (array $values): array => $usersQuery
                            ->whereKey($values)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->searchable()
                    ->default(
                        fn () => $this->conversation
                            ->managers()
                            ->whereNot('id', auth()->id())
                            ->pluck('id')
                            ->all()
                    ),
            ])
            ->action(function (array $data) {
                /** @var User $user */
                $user = auth()->user();

                if (! $this->conversation->managers()->find($user)) {
                    return;
                }

                $this->conversation->channel_name = $data['name'];
                $this->conversation->is_private_channel = $data['is_private'];
                $this->conversation->save();

                collect($data['managers'])
                    ->each(fn (string $id) => app(PromoteUserToChannelManager::class)(user: User::find($id), conversation: $this->conversation));

                $this->conversation->managers()
                    ->whereNotIn('id', $data['managers'])
                    ->whereNot('id', $user->id)
                    ->each(fn (User $demote) => app(DemoteUserFromChannelManager::class)(user: $demote, conversation: $this->conversation));

                Notification::make()
                    ->title('Channel updated.')
                    ->success()
                    ->send();
            });
    }

    public function deleteChannelAction(): Action
    {
        return Action::make('deleteChannel')
            ->color('danger')
            ->link()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalHeading('Are you sure you want to delete this channel?')
            ->modalDescription('This action will permanently delete all chats and information contained within in the channel and cannot be undone.')
            ->action(function (DeleteTwilioConversation $deleteTwilioConversation) {
                if ($this->conversation->type !== ConversationType::Channel) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                if (! $this->conversation->managers()->find($user)) {
                    return;
                }

                if ($deleteTwilioConversation(conversation: $this->conversation)) {
                    $this->selectConversation(null);

                    Notification::make()
                        ->title('Channel deleted.')
                        ->success()
                        ->send();
                }
            });
    }

    public function onMessageSent(User $author, string $messageSid, array $messageContent): void
    {
        if ($author->is(auth()->user())) {
            dispatch(new NotifyConversationParticipants(
                new ConversationMessageSent(
                    $this->conversation,
                    auth()->user(),
                    $messageSid,
                    $messageContent,
                ),
            ));

            return;
        }

        $this->clearNotifications();
    }

    public function joinChannelsAction()
    {
        $channels = TwilioConversation::query()
            ->whereDoesntHave('participants', fn (Builder $query) => $query->whereKey(auth()->id()))
            ->where('type', ConversationType::Channel)
            ->where('is_private_channel', false)
            ->whereNotNull('channel_name')
            ->orderBy('channel_name')
            ->pluck('channel_name', 'sid')
            ->all();

        return Action::make('joinChannels')
            ->label('Join Channels')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Join channels')
            ->modalDescription(empty($channels) ? 'There are no channels to join.' : null)
            ->when(empty($channels), fn (Action $action) => $action->modalSubmitAction(false))
            ->form([
                CheckboxList::make('channels')
                    ->hiddenLabel()
                    ->searchable()
                    ->options($channels)
                    ->columns()
                    ->hidden(empty($channels)),
            ])
            ->action(function (AddUserToConversation $addUserToConversation, array $data) {
                $channels = TwilioConversation::find($data['channels'] ?? []);

                foreach ($channels as $channel) {
                    if ($channel->type !== ConversationType::Channel) {
                        continue;
                    }

                    if ($channel->is_private_channel) {
                        continue;
                    }

                    /** @var User $user */
                    $user = auth()->user();

                    $addUserToConversation(
                        user: $user,
                        conversation: $channel,
                    );
                }

                Notification::make()
                    ->title($channels->count() > 1 ? 'Channels joined.' : 'Channel joined.')
                    ->success()
                    ->send();

                if ($channels->count() === 1) {
                    $this->selectConversation($channels->first());
                }
            });
    }

    public function leaveChannelAction(): Action
    {
        $action = Action::make('leaveChannel')
            ->color('danger')
            ->link()
            ->icon('heroicon-m-arrow-right-start-on-rectangle')
            ->requiresConfirmation()
            ->modalHeading('Are you sure you want to leave?')
            ->modalDescription('You will no longer have access to these messages, unless you are invited back.')
            ->action(function (RemoveUserFromConversation $removeUserFromConversation) {
                if ($this->conversation->type !== ConversationType::Channel) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                if ($removeUserFromConversation(user: $user, conversation: $this->conversation)) {
                    $this->selectConversation(null);

                    Notification::make()
                        ->title('Left channel.')
                        ->success()
                        ->send();
                }
            });

        /** @var User $user */
        $user = auth()->user();

        if ($this->conversation->managers()->find($user)) {
            if ($this->conversation->managers()->whereKeyNot($user->getKey())->exists()) {
                $action->modalDescription(
                    new HtmlString(
                        "You will be removed as a channel manager.<br>{$action->getModalDescription()}"
                    )
                );
            } else {
                $action->modalHeading('Unable to leave channel.')
                    ->modalDescription('You cannot leave a channel where you are the only manager.')
                    ->action(null)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close');
            }
        }

        return $action;
    }

    public function updateNotificationPreferenceAction(): Action
    {
        $participation = $this->conversation->participants()->find(auth()->id())?->participant;

        return Action::make('updateNotificationPreference')
            ->label('Notifications')
            ->link()
            ->color($participation?->notification_preference->getColor() ?? 'warning')
            ->icon($participation?->notification_preference->getIcon() ?? 'heroicon-m-bell')
            ->modalHeading('Notifications preference')
            ->modalWidth('sm')
            ->modalSubmitActionLabel('Update')
            ->fillForm(fn (): array => [
                'preference' => $participation?->notification_preference,
            ])
            ->form([
                Radio::make('preference')
                    ->hiddenLabel()
                    ->options(ConversationNotificationPreference::class)
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->conversation->participants()->updateExistingPivot(auth()->id(), [
                    'notification_preference' => $data['preference'],
                ]);

                Notification::make()
                    ->title('Notification preferences updated')
                    ->success()
                    ->send();
            });
    }

    public function addUserToChannelAction(): Action
    {
        $usersQuery = User::query()
            ->where('id', '!=', auth()->id())
            ->whereDoesntHave('conversations', fn (Builder $query) => $query->whereKey($this->conversation));

        return Action::make('addUserToChannel')
            ->label('Invite users')
            ->link()
            ->icon('heroicon-m-user-plus')
            ->modalWidth('sm')
            ->modalDescription('They will have access to the entire conversation history.')
            ->modalSubmitActionLabel('Invite')
            ->form([
                Select::make('users')
                    ->label('Pick users to invite')
                    ->multiple()
                    ->options(
                        fn (): array => $usersQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $usersQuery
                            ->where('name', 'ilike', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelsUsing(
                        fn (array $values): array => $usersQuery
                            ->whereKey($values)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->searchable(),
            ])
            ->action(function (AddUserToConversation $addUserToConversation, array $data) {
                if ($this->conversation->type !== ConversationType::Channel) {
                    return;
                }

                $users = User::find($data['users']);

                foreach ($users as $user) {
                    $addUserToConversation(
                        user: $user,
                        conversation: $this->conversation,
                    );
                }

                Notification::make()
                    ->title($users->count() > 1 ? 'Users invited to channel.' : 'User invited to channel.')
                    ->success()
                    ->send();
            });
    }

    public function selectConversation(?TwilioConversation $conversation): void
    {
        $this->conversationId = $conversation?->getKey();
        $this->conversation = $conversation;
        $this->loadConversationActiveUsers();

        $this->clearNotifications();
    }

    #[Renderless]
    public function loadConversationActiveUsers(): void
    {
        auth()->user()->update([
            'last_chat_ping_at' => now(),
        ]);

        if (! $this->conversation) {
            $this->conversationActiveUsers = [];

            return;
        }

        $this->conversationActiveUsers = $this->conversation
            ->participants()
            ->where('last_chat_ping_at', '>', now()->subMinutes(3))
            ->pluck('id')
            ->all();
    }

    #[Renderless]
    public function generateToken(bool $bustCache = false): string
    {
        if ($bustCache) {
            Cache::forget('{twilio_access_token_' . auth()->id() . '}');
        }

        /** @var AccessToken $token */
        $token = Cache::remember('{twilio_access_token_' . auth()->id() . '}', 21500, function () {
            $apiKey = app(GetTwilioApiKey::class)();

            $twilioClient = app(Client::class);

            $settings = app(TwilioSettings::class);

            $configuration = $twilioClient->conversations->v1->configuration()->fetch();

            return (new AccessToken(
                accountSid: $settings->account_sid,
                signingKeySid: $apiKey->api_sid,
                secret: $apiKey->secret,
                ttl: 21600, // 6 hours
                identity: auth()->id(),
            ))
                ->addGrant((new ChatGrant())->setServiceSid($configuration->defaultChatServiceSid));
        });

        return $token->toJWT();
    }

    #[Renderless]
    public function getUserAvatarUrl(string $userId): ?string
    {
        $user = User::find($userId);

        if (! $user) {
            return null;
        }

        return filament()->getUserAvatarUrl($user);
    }

    #[Renderless]
    public function getUserName(string $userId): ?string
    {
        $user = User::find($userId);

        return $user?->name;
    }

    #[Renderless]
    public function handleError(mixed $error): void
    {
        if (! $error instanceof Exception) {
            $error = new Exception(json_encode($error));
        }

        report($error);

        Notification::make()
            ->title('Something went wrong. If this issue persists, please contact support.')
            ->danger()
            ->send();
    }

    protected function clearNotifications(): void
    {
        $participation = $this->conversation?->participants()->find(auth()->user())?->participant;

        if (! $participation) {
            return;
        }

        $participation->first_unread_message_sid = null;
        $participation->first_unread_message_at = null;
        $participation->last_unread_message_content = null;
        $participation->last_read_at = now();
        $participation->unread_messages_count = 0;
        $participation->save();
    }

    protected function getViewData(): array
    {
        if (Integration::Twilio->isOff()) {
            return [
                'integration' => Integration::Twilio,
            ];
        }

        return [
            'users' => $this->conversation?->participants()->pluck('name', 'id')->all() ?? [],
        ];
    }
}
