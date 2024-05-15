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

namespace AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns;

use Exception;
use App\Models\User;
use Filament\Actions\Action;
use Illuminate\Http\JsonResponse;
use Livewire\Attributes\Computed;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Symfony\Component\HttpFoundation\Response;
use AdvisingApp\Assistant\Models\AssistantChat;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Assistant\Models\AssistantChatFolder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait CanManageFolders
{
    #[Computed]
    public function folders(): EloquentCollection
    {
        return AssistantChatFolder::query()
            ->whereBelongsTo(auth()->user())
            ->with([
                'chats' => fn (HasMany $query) => $query->orderByDesc('updated_at'),
            ])
            ->orderBy('name')
            ->get();
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
                auth()->user()->assistantChatFolders()->find($arguments['folder'])
                    ?->update(['name' => $data['name']]);

                unset($this->folders, $this->threads);
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
                auth()->user()->assistantChatFolders()->find($arguments['folder'])
                    ?->delete();

                unset($this->folders, $this->threads);
            })
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function moveThreadAction(): Action
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
                $chat = auth()->user()->assistantChats()->find($arguments['chat']);

                if (! $chat) {
                    return;
                }

                $folder = auth()->user()->assistantChatFolders()->find($data['folder']);

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
        $chat = auth()->user()->assistantChats()->find($chatId);

        if (! $chat) {
            return response()->json([
                'success' => false,
                'message' => 'Chat could not be found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $folder = auth()->user()->assistantChatFolders()->find($folderId);

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

    public function deleteThreadAction(): Action
    {
        return Action::make('deleteChat')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $chat = auth()->user()->assistantChats()->find($arguments['chat']);

                if (! $chat) {
                    return;
                }

                $chat->delete();

                if ($this->thread->id === $arguments['chat']) {
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

        unset($this->folders, $this->threads);
    }
}
