<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Assist\Team\Models\Team;
use Filament\Actions\Action;
use Livewire\Attributes\Rule;
use App\Filament\Pages\Dashboard;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Assist\Assistant\Models\AssistantChat;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\Assistant\Models\AssistantChatMessage;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

class PersonalAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.ai-assistant';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 1;

    public Collection $chats;

    public Chat $chat;

    #[Rule(['required', 'string'])]
    public string $message = '';

    public string $prompt = '';

    public bool $showCurrentResponse = false;

    public string $currentResponse = '';

    public bool $renderError = false;

    public string $error = '';

    public ConsentAgreement $consentAgreement;

    public bool $consentedToTerms = false;

    public bool $loading = true;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('assistant.access');
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->authorize('assistant.access');

        $this->consentAgreement = ConsentAgreement::query()
            ->where('type', ConsentAgreementType::AzureOpenAI)
            ->first();

        $this->chats = $user->assistantChats()->latest()->get();

        /** @var AssistantChat $chat */
        $chat = $this->chats->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    public function determineIfConsentWasGiven(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasNotConsentedTo($this->consentAgreement)) {
            $this->dispatch('open-modal', id: 'consent-agreement');
        } else {
            $this->consentedToTerms = true;
        }

        $this->loading = false;
    }

    public function confirmConsent(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($this->consentedToTerms === false) {
            return;
        }

        $user->consentTo($this->consentAgreement);

        $this->dispatch('close-modal', id: 'consent-agreement');
    }

    public function denyConsent(): void
    {
        $this->redirect(Dashboard::getUrl());
    }

    public function sendMessage(): void
    {
        $this->showCurrentResponse = true;

        $this->reset('renderError');
        $this->reset('error');

        $this->validate();

        $this->prompt = $this->message;

        $this->message = '';

        $this->setMessage($this->prompt, AIChatMessageFrom::User);

        $this->js('$wire.ask()');
    }

    #[On('ask')]
    public function ask(AIChatClient $ai): void
    {
        try {
            $this->currentResponse = $ai->ask($this->chat, function (string $partial) {
                $this->stream('currentResponse', nl2br($partial));
            });
        } catch (ContentFilterException|TokensExceededException $e) {
            $this->renderError = true;
            $this->error = $e->getMessage();
        }

        $this->reset('showCurrentResponse');

        if ($this->renderError === false) {
            $this->setMessage($this->currentResponse, AIChatMessageFrom::Assistant);
        }

        $this->reset('currentResponse');
    }

    public function saveChatAction(): Action
    {
        return Action::make('saveChat')
            ->label('Save')
            ->modalHeading('Save chat')
            ->modalSubmitActionLabel('Save')
            ->icon('heroicon-s-bookmark')
            ->link()
            ->size(ActionSize::Small)
            ->form(
                [
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Name this chat')
                        ->required(),
                ]
            )
            ->modalWidth('md')
            ->action(function (array $data) {
                if (filled($this->chat->id)) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                /** @var AssistantChat $assistantChat */
                $assistantChat = $user->assistantChats()->create(['name' => $data['name']]);

                $this->chat->messages->each(function (ChatMessage $message) use ($assistantChat) {
                    $assistantChat->messages()->create($message->toArray());
                });

                $this->chat->id = $assistantChat->id;

                $this->chats->prepend($assistantChat);
            });
    }

    public function selectChat(AssistantChat $chat): void
    {
        $this->reset(['message', 'prompt', 'renderError', 'error']);

        $this->chat = new Chat(
            id: $chat->id ?? null,
            messages: ChatMessage::collection($chat->messages ?? []),
        );
    }

    public function newChat(): void
    {
        $this->reset(['message', 'prompt', 'renderError', 'error']);

        $this->chat = new Chat(id: null, messages: ChatMessage::collection([]));
    }

    public function deleteChatAction(): Action
    {
        return Action::make('deleteChat')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $chat = AssistantChat::find($arguments['chat']);

                $chat?->delete();

                $this->chats = $this->chats->filter(fn (AssistantChat $chat) => $chat->id !== $arguments['chat']);

                if ($this->chat->id === $arguments['chat']) {
                    $this->newChat();
                }
            })
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function editChatAction(): Action
    {
        return Action::make('editChat')
            ->modalSubmitActionLabel('Save')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form(
                [
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Rename this chat')
                        ->required(),
                ]
            )
            ->action(function (array $arguments, array $data) {
                $chat = AssistantChat::find($arguments['chat']);

                $chat?->update($data);

                $this->chats = $this->chats->map(function (AssistantChat $chat) use ($arguments, $data) {
                    if ($chat->id === $arguments['chat']) {
                        $chat->name = $data['name'];
                    }

                    return $chat;
                });
            })
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function shareChatAction(): Action
    {
        return Action::make('shareChat')
            ->modalSubmitActionLabel('Share')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                Select::make('target_type')
                    ->label('Type')
                    ->options([
                        'team' => 'Team',
                        'user' => 'User',
                    ])
                    ->default('user')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->live(),
                Select::make('target_ids')
                    ->label('Targets')
                    ->options(fn (Get $get): Collection => match ($get('target_type')) {
                        'team' => Team::orderBy('name')->pluck('name', 'id'),
                        'user' => User::whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
                        default => collect(),
                    })
                    ->searchable()
                    ->multiple()
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                $chat = AssistantChat::find($arguments['chat']);

                $users = match ($data['target_type']) {
                    'team' => collect($data['target_ids'])
                        ->map(fn ($id) => Team::find($id)->users()->whereKeyNot(auth()->id())->get())
                        ->flatten()
                        ->unique(),
                    'user' => User::whereIn('id', $data['target_ids'])->get(),
                };

                $users
                    ->each(
                        function (User $user) use ($chat) {
                            $replica = $chat
                                ->replicate(['id', 'user_id'])
                                ->user()
                                ->associate($user);

                            $replica->save();

                            $chat
                                ->messages()
                                ->each(
                                    fn (AssistantChatMessage $message) => $message
                                        ->replicate(['id', 'assistant_chat_id'])
                                        ->chat()
                                        ->associate($replica)
                                        ->save()
                                );

                            Notification::make()
                                ->success()
                                ->title("You shared an assistant chat with {$user->name}.")
                                ->sendToDatabase(auth()->user());
                        }
                    );
            })
            ->icon('heroicon-o-share')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    protected function setMessage(string $message, AIChatMessageFrom $from): void
    {
        if (filled($this->chat->id)) {
            /** @var User $user */
            $user = auth()->user();

            /** @var AssistantChat $assistantChat */
            $assistantChat = $user->assistantChats()->findOrFail($this->chat->id);

            $assistantChat->messages()->create([
                'message' => $message,
                'from' => $from,
            ]);
        }

        $this->chat->messages[] = new ChatMessage(
            message: $message,
            from: $from,
        );
    }
}
