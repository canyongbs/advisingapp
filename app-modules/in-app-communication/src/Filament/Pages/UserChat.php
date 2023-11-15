<?php

namespace Assist\InAppCommunication\Filament\Pages;

use App\Models\User;
use Twilio\Rest\Client;
use Filament\Pages\Page;
use Twilio\Jwt\AccessToken;
use Filament\Actions\Action;
use Twilio\Jwt\Grants\ChatGrant;
use Livewire\Attributes\Renderless;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
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

    public ?Collection $conversations = null;

    public ?string $selectedConversation = null;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'in-app-communication::filament.pages.user-chat';

    public function mount()
    {
        $this->conversations = auth()->user()->conversations;
    }

    public function newChatAction()
    {
        return Action::make('newChat')
            ->label('New Chat')
            ->icon('heroicon-m-plus')
            ->form([
                Select::make('user')
                    ->options(
                        User::where('id', '!=', auth()->user()->id)
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

                $this->selectedConversation = $conversation;
            });
    }

    public function selectConversation(string $conversationSid): void
    {
        $this->selectedConversation = $conversationSid;
    }

    #[Renderless]
    public function generateToken(): string
    {
        $apiKey = app(GetTwilioApiKey::class)();

        $twilioClient = app(Client::class);

        $configuration = $twilioClient->conversations->v1->configuration()->fetch();

        $token = (new AccessToken(
            accountSid: config('services.twilio.account_sid'),
            signingKeySid: $apiKey->api_sid,
            secret: $apiKey->secret,
            identity: auth()->user()->id,
        ))
            ->addGrant((new ChatGrant())->setServiceSid($configuration->defaultChatServiceSid));

        return $token->toJWT();
    }

    public function getUserAvatarUrl(string $userId): string
    {
        return filament()->getUserAvatarUrl(User::findOrFail($userId));
    }
}
