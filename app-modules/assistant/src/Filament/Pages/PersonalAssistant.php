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

namespace AdvisingApp\Assistant\Filament\Pages;

use App\Models\User;
use Exception;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Illuminate\Http\JsonResponse;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Rule;
use AdvisingApp\Team\Models\Team;
use App\Filament\Pages\Dashboard;
use Livewire\Attributes\Computed;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\ActionSize;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Enums\ConsentAgreementType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Assistant\Models\AssistantChatFolder;
use AdvisingApp\Assistant\Enums\AssistantChatShareVia;
use AdvisingApp\Assistant\Jobs\ShareAssistantChatsJob;
use AdvisingApp\Assistant\Enums\AssistantChatShareWith;
use AdvisingApp\IntegrationAI\Client\Contracts\AIChatClient;
use AdvisingApp\IntegrationAI\Exceptions\ContentFilterException;
use AdvisingApp\IntegrationAI\DataTransferObjects\DynamicContext;
use AdvisingApp\IntegrationAI\Exceptions\TokensExceededException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use AdvisingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property EloquentCollection $chats
 */
class PersonalAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.personal-assistant';

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?int $navigationSort = 30;

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

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->can('assistant.access');
    }

    public function mount(): void
    {
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

        if (! $user->defaultAssistantChatFoldersHaveBeenCreated()) {
            foreach (AssistantChatFolder::defaults() as $default) {
                $user->assistantChatFolders()->create([
                    'name' => $default,
                ]);
            }

            $user->update([
                'default_assistant_chat_folders_created' => true,
            ]);
        }

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
            /** @var User $user */
            $user = auth()->user();

            $this->currentResponse = $ai
                ->provideDynamicContext(new DynamicContext($user))
                ->ask($this->chat, function (string $partial) {
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
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Name this chat')
                    ->required(),
                $this->folderSelect(),
            ])
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

                $folder = AssistantChatFolder::find($data['folder']);

                $this->moveChat($assistantChat, $folder);
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
                    ->autocomplete(false)
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
                    ->autocomplete(false)
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
                $this->folderSelect(),
            ])
            ->action(function (array $arguments, array $data) {
                $chat = AssistantChat::find($arguments['chat']);
                $folder = AssistantChatFolder::find($data['folder']);

                $this->moveChat($chat, $folder);
            })
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function movedChat(string $chatId, ?string $folderId): JsonResponse
    {
        $chat = AssistantChat::find($chatId);
        $folder = $folderId ? AssistantChatFolder::find($folderId) : null;

        try {
            $this->moveChat($chat, $folder);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Chat could not be moved. Something went wrong, if this continues please contact support.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chat moved successfully.',
        ], Response::HTTP_OK);
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
                    ->autocomplete(false)
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

    public function cloneChatAction(): Action
    {
        return Action::make('cloneChat')
            ->label('Clone')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('target_type')
                    ->label('With')
                    ->options(AssistantChatShareWith::class)
                    ->enum(AssistantChatShareWith::class)
                    ->default(AssistantChatShareWith::default())
                    ->required()
                    ->live(),
                Select::make('target_ids')
                    ->label(fn (Get $get): string => match ($get('target_type')) {
                        AssistantChatShareWith::Team => 'Select Teams',
                        AssistantChatShareWith::User => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('target_type')))
                    ->options(function (Get $get): Collection {
                        return match ($get('target_type')) {
                            AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                            AssistantChatShareWith::User => User::whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
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

                dispatch(new ShareAssistantChatsJob($chat, AssistantChatShareVia::Internal, $data['target_type'], $data['target_ids'], $sender));
            })
            ->link()
            ->icon('heroicon-m-document-duplicate')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }

    public function emailChatAction(): Action
    {
        return Action::make('emailChat')
            ->label('Email')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('target_type')
                    ->label('With')
                    ->options(AssistantChatShareWith::class)
                    ->enum(AssistantChatShareWith::class)
                    ->default(AssistantChatShareWith::default())
                    ->required()
                    ->live(),
                Select::make('target_ids')
                    ->label(fn (Get $get): string => match ($get('target_type')) {
                        AssistantChatShareWith::Team => 'Select Teams',
                        AssistantChatShareWith::User => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('target_type')))
                    ->options(function (Get $get): Collection {
                        return match ($get('target_type')) {
                            AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                            AssistantChatShareWith::User => User::orderBy('name')->pluck('name', 'id'),
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

                dispatch(new ShareAssistantChatsJob($chat, AssistantChatShareVia::Email, $data['target_type'], $data['target_ids'], $sender));
            })
            ->link()
            ->icon('heroicon-m-envelope')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
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

    private function folderSelect(): Select
    {
        return Select::make('folder')
            ->options(function () {
                /** @var User $user */
                $user = auth()->user();

                return $user
                    ->assistantChatFolders()
                    ->orderBy('name')
                    ->pluck('name', 'id');
            })
            ->placeholder('-');
    }

    private function moveChat(AssistantChat $chat, ?AssistantChatFolder $folder): void
    {
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
    }
}
