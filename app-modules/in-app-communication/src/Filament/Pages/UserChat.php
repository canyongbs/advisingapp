<?php

namespace Assist\InAppCommunication\Filament\Pages;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use Filament\Pages\Page;
use Twilio\Jwt\AccessToken;
use Filament\Actions\Action;
use Twilio\Jwt\Grants\ChatGrant;
use Livewire\Attributes\Renderless;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Assist\InAppCommunication\Enums\ConversationType;
use Assist\IntegrationTwilio\Actions\GetTwilioApiKey;
use Assist\InAppCommunication\Actions\CreateTwilioConversation;

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

    public function mount()
    {
        $this->conversations = auth()->user()->conversations;
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
            Cache::forget('twilio_access_token_' . auth()->id());
        }

        /** @var AccessToken $token */
        $token = Cache::remember('twilio_access_token_' . auth()->id(), 21500, function () {
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
