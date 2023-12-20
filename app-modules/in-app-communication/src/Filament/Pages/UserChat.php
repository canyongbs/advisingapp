<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

use Exception;
use App\Models\User;
use App\Enums\Feature;
use Twilio\Rest\Client;
use Filament\Pages\Page;
use Twilio\Jwt\AccessToken;
use Filament\Actions\Action;
use Twilio\Jwt\Grants\ChatGrant;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use AdvisingApp\InAppCommunication\Enums\ConversationType;
use AdvisingApp\IntegrationTwilio\Actions\GetTwilioApiKey;
use AdvisingApp\InAppCommunication\Actions\CreateTwilioConversation;

class UserChat extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public array $chats = [];

    public string $chatId = '';

    public Collection $conversations;

    public ?string $selectedConversation = null;

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static string $view = 'in-app-communication::filament.pages.user-chat';

    protected static ?string $title = 'Realtime Chat';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return Gate::check(Feature::RealtimeChat->getGateName()) && $user->can('in-app-communication.realtime-chat.access');
    }

    public function mount()
    {
        $this->authorize(Feature::RealtimeChat->getGateName());
        $this->authorize('in-app-communication.realtime-chat.access');

        /** @var User $user */
        $user = auth()->user();

        $this->conversations = $user->conversations;
    }

    public function newChatAction()
    {
        return Action::make('newChat')
            ->label('New Chat')
            ->icon('heroicon-m-plus')
            ->modalWidth('sm')
            ->form([
                Select::make('user')
                    ->options(
                        User::where('id', '!=', auth()->user()->id)
                            ->whereDoesntHave(
                                'conversations',
                                fn ($query) => $query
                                    ->where('type', ConversationType::UserToUser)
                                    ->whereHas(
                                        'participants',
                                        fn ($query) => $query->where('user_id', auth()->user()->id)
                                    )
                            )
                            ->pluck('name', 'id')
                    )
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $users = collect(
                    [
                        auth()->user(),
                        User::findOrFail($data['user']),
                    ]
                );

                $conversation = app(CreateTwilioConversation::class)(type: ConversationType::UserToUser, users: $users);

                $this->conversations->push($conversation);
                $this->selectedConversation = $conversation->sid;
            });
    }

    public function selectConversation(string $conversationSid): void
    {
        $this->selectedConversation = $conversationSid;
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

            $configuration = $twilioClient->conversations->v1->configuration()->fetch();

            return (new AccessToken(
                accountSid: config('services.twilio.account_sid'),
                signingKeySid: $apiKey->api_sid,
                secret: $apiKey->secret,
                ttl: 21600, // 6 hours
                identity: auth()->user()->id,
            ))
                ->addGrant((new ChatGrant())->setServiceSid($configuration->defaultChatServiceSid));
        });

        return $token->toJWT();
    }

    #[Renderless]
    public function getUserAvatarUrl(string $userId): string
    {
        return filament()->getUserAvatarUrl(User::findOrFail($userId));
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
}
