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
use Livewire\Attributes\Computed;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Assist\Assistant\Models\AssistantChat;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\Assistant\Models\AssistantChatFolder;
use Assist\Assistant\Enums\AssistantChatShareVia;
use Assist\Assistant\Jobs\ShareAssistantChatsJob;
use Assist\Assistant\Enums\AssistantChatShareWith;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

/**
 * @property EloquentCollection $chats
 */
class PersonalAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.personal-assistant';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 1;

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
        $this->authorize('assistant.access');

        $this->consentAgreement = ConsentAgreement::where('type', ConsentAgreementType::AzureOpenAI)->first();

        /** @var AssistantChat $chat */
        $chat = $this->chats->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    #[Computed]
    public function chats(): EloquentCollection
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->assistantChats()
            ->doesntHave('folder')
            ->latest()
            ->get();
    }

    #[Computed]
    public function folders(): EloquentCollection
    {
        return AssistantChatFolder::whereRelation('user', 'id', auth()->id())
            ->with([
                'chats' => fn (HasMany $query) => $query->orderByDesc('updated_at'),
            ])
            ->orderBy('name')
            ->get();
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

    public function newFolderAction(): Action
    {
        return Action::make('newFolder')
            ->label('New Folder')
            ->modalSubmitActionLabel('Create')
            ->modalWidth('md')
            ->form([
                TextInput::make('name')
                    ->required()
                    ->unique(AssistantChatFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('user_id', auth()->id());
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                $folder = new AssistantChatFolder(['name' => $data['name']]);

                /** @var User $user */
                $user = auth()->user();
                $folder->user()->associate($user);
                $folder->save();
            })
            ->icon('heroicon-m-folder-plus')
            ->color('primary')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }

    public function renameFolderAction(): Action
    {
        return Action::make('renameFolder')
            ->modalSubmitActionLabel('Rename')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Rename this folder')
                    ->required()
                    ->unique(AssistantChatFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('user_id', auth()->id());
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                AssistantChatFolder::find($arguments['folder'])
                    ->update(['name' => $data['name']]);

                unset($this->folders, $this->chats);
            })
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function deleteFolderAction(): Action
    {
        return Action::make('deleteFolder')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->modalDescription('Are you sure you wish to delete this folder? Any chats stored within this folder will also be deleted and this action is not reversible.')
            ->action(function (array $arguments) {
                AssistantChatFolder::find($arguments['folder'])
                    ->delete();

                unset($this->folders, $this->chats);
            })
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function moveChatAction(): Action
    {
        return Action::make('moveChat')
            ->label('Move chat to a different folder')
            ->modalSubmitActionLabel('Move')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                Select::make('folder')
                    ->options(function () {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user
                            ->assistantChatFolders()
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->placeholder('-'),
            ])
            ->action(function (array $arguments, array $data) {
                $chat = AssistantChat::find($arguments['chat']);
                $folder = AssistantChatFolder::find($data['folder']);

                if ($folder) {
                    $chat->folder()
                        ->associate($folder)
                        ->save();
                } else {
                    $chat->folder()
                        ->disassociate()
                        ->save();
                }

                unset($this->folders, $this->chats);
            })
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function deleteChatAction(): Action
    {
        return Action::make('deleteChat')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $chat = AssistantChat::find($arguments['chat']);

                $chat?->delete();

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
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Rename this chat')
                    ->required(),
            ])
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
                Select::make('via')
                    ->label('Via')
                    ->options(AssistantChatShareVia::class)
                    ->enum(AssistantChatShareVia::class)
                    ->default(AssistantChatShareVia::default())
                    ->required()
                    ->selectablePlaceholder(false)
                    ->live(),
                Select::make('target_type')
                    ->label('Type')
                    ->options(AssistantChatShareWith::class)
                    ->enum(AssistantChatShareWith::class)
                    ->default(AssistantChatShareWith::default())
                    ->required()
                    ->selectablePlaceholder(false)
                    ->live(),
                Select::make('target_ids')
                    ->label('Targets')
                    ->options(function (Get $get): Collection {
                        return match ($get('via')) {
                            AssistantChatShareVia::Email => match ($get('target_type')) {
                                AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                                AssistantChatShareWith::User => User::orderBy('name')->pluck('name', 'id'),
                            },
                            AssistantChatShareVia::Internal => match ($get('target_type')) {
                                AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                                AssistantChatShareWith::User => User::whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
                            },
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                /** @var User $sender */
                $sender = auth()->user();

                $chat = AssistantChat::find($arguments['chat']);

                dispatch(new ShareAssistantChatsJob($chat, $data['via'], $data['target_type'], $data['target_ids'], $sender));
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
