<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Research\Filament\Pages\ManageResearchRequests\Concerns;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestFolder;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

trait CanManageFolders
{
    /**
     * @var array<mixed>
     */
    #[Locked]
    public array $folders = [];

    public function mountCanManageFolders(): void
    {
        $this->folders = $this->getFolders();
    }

    /**
     * @return array<mixed>
     */
    public function getFolders(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->researchRequestFolders()
            ->with([/** @phpstan-ignore argument.type */                
                'requests' => fn (HasMany $query) => $query
                    ->latest('updated_at'),
            ])
            ->orderBy('name')
            ->get()
            ->toArray();
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
                    ->unique(ResearchRequestFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('user_id', auth()->id());
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                $folder = new ResearchRequestFolder();
                $folder->name = $data['name'];
                $folder->user()->associate(auth()->user());
                $folder->save();

                $this->folders = $this->getFolders();
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
                'name' => auth()->user()->researchRequestFolders()
                    ->find($arguments['folder'])
                    ?->name,
            ])
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Rename this folder')
                    ->required()
                    ->unique(ResearchRequestFolder::class, modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('user_id', auth()->id());
                    }),
            ])
            ->action(function (array $arguments, array $data) {
                auth()->user()->researchRequestFolders()
                    ->find($arguments['folder'])
                    ?->update(['name' => $data['name']]);

                $this->folders = $this->getFolders();
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
            ->modalDescription('Are you sure you wish to delete this folder?')
            ->action(function (array $arguments) {
                auth()->user()->researchRequestFolders()
                    ->find($arguments['folder'])
                    ?->delete();

                $this->folders = $this->getFolders();
            })
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function moveRequestAction(): Action
    {
        return Action::make('moveRequest')
            ->label('Move request to a different folder')
            ->modalSubmitActionLabel('Move')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                $this->folderSelect(),
            ])
            ->action(function (array $arguments, array $data) {
                $request = auth()->user()->researchRequests()
                    ->find($arguments['request']);

                if (! $request) {
                    return;
                }

                $folder = auth()->user()->researchRequestFolders()
                    ->find($data['folder']);

                $this->moveRequest($request, $folder);
            })
            ->icon('heroicon-m-arrow-down-on-square')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function movedRequest(?string $requestId, ?string $folderId): ?JsonResponse
    {
        if (blank($requestId)) {
            return null;
        }

        $request = auth()->user()->researchRequests()
            ->find($requestId);

        if (! $request) {
            return response()->json([
                'success' => false,
                'message' => 'Request could not be found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $folder = filled($folderId) ?
            auth()->user()->researchRequestFolders()
                ->find($folderId) :
            null;

        try {
            $this->moveRequest($request, $folder);
        } catch (Exception $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Request could not be moved. Something went wrong, if this continues please contact support.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request moved successfully.',
        ], Response::HTTP_OK);
    }

    protected function folderSelect(): Select
    {
        return Select::make('folder')
            ->options(fn (): array => auth()->user()
                ->researchRequestFolders()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all())
            ->placeholder('-');
    }

    protected function moveRequest(ResearchRequest $request, ?ResearchRequestFolder $folder): void
    {
        if ($folder) {
            $request->folder()
                ->associate($folder)
                ->save();
        } else {
            $request->folder()
                ->disassociate()
                ->save();
        }

        $this->requestsWithoutAFolder = $this->getRequestsWithoutAFolder();
        $this->folders = $this->getFolders();
    }
}
