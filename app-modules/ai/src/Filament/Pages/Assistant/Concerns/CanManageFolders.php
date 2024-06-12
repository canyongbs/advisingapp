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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use Exception;
use Filament\Actions\Action;
use Illuminate\Http\JsonResponse;
use Livewire\Attributes\Computed;
use Filament\Actions\StaticAction;
use AdvisingApp\Ai\Models\AiThread;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Ai\Models\AiThreadFolder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait CanManageFolders
{
    #[Computed]
    public function folders(): EloquentCollection
    {
        return auth()->user()
            ->aiThreadFolders()
            ->where('application', static::APPLICATION)
            ->with([
                'threads' => fn (HasMany $query) => $query->latest('updated_at')->withMax('messages', 'created_at'),
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
                    ->unique(AiThreadFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('user_id', auth()->id())
                            ->where('application', static::APPLICATION);
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                $folder = new AiThreadFolder();
                $folder->name = $data['name'];
                $folder->application = static::APPLICATION;
                $folder->user()->associate(auth()->user());
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
            ->fillForm(fn (array $arguments) => [
                'name' => auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($arguments['folder'])
                    ?->name,
            ])
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Rename this folder')
                    ->required()
                    ->unique(AiThreadFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('user_id', auth()->id())
                            ->where('application', static::APPLICATION);
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($arguments['folder'])
                    ?->update(['name' => $data['name']]);

                unset($this->folders);
            })
            ->icon('heroicon-m-pencil')
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
                auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($arguments['folder'])
                    ?->delete();

                unset($this->folders);
            })
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function moveThreadAction(): Action
    {
        return Action::make('moveThread')
            ->label('Move chat to a different folder')
            ->modalSubmitActionLabel('Move')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                $this->folderSelect(),
            ])
            ->action(function (array $arguments, array $data) {
                $thread = auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread']);

                if (! $thread) {
                    return;
                }

                $folder = auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($data['folder']);

                $this->moveThread($thread, $folder);
            })
            ->icon('heroicon-m-arrow-down-on-square')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function movedThread(?string $threadId, ?string $folderId): ?JsonResponse
    {
        if (blank($threadId)) {
            return null;
        }

        $thread = auth()->user()->aiThreads()
            ->whereRelation('assistant', 'application', static::APPLICATION)
            ->find($threadId);

        if (! $thread) {
            return response()->json([
                'success' => false,
                'message' => 'Chat could not be found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $folder = filled($folderId) ?
            auth()->user()->aiThreadFolders()
                ->where('application', static::APPLICATION)
                ->find($folderId) :
            null;

        try {
            $this->moveThread($thread, $folder);
        } catch (Exception $exception) {
            report($exception);

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

    protected function folderSelect(): Select
    {
        return Select::make('folder')
            ->options(fn (): array => auth()->user()
                ->aiThreadFolders()
                ->where('application', static::APPLICATION)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all())
            ->placeholder('-');
    }

    protected function moveThread(AiThread $thread, ?AiThreadFolder $folder): void
    {
        if ($folder) {
            $thread->folder()
                ->associate($folder)
                ->save();
        } else {
            $thread->folder()
                ->disassociate()
                ->save();
        }

        unset($this->threadsWithoutAFolder, $this->folders);
    }
}
