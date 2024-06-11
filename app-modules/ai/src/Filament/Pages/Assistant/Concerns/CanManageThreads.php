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

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Livewire\Attributes\Locked;
use AdvisingApp\Team\Models\Team;
use Livewire\Attributes\Computed;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiThread;
use Livewire\Attributes\Renderless;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use AdvisingApp\Ai\Models\AiAssistant;
use Filament\Support\Enums\ActionSize;
use AdvisingApp\Ai\Actions\CreateThread;
use AdvisingApp\Ai\Actions\DeleteThread;
use App\Models\Scopes\WithoutSuperAdmin;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Ai\Rules\RestrictSuperAdmin;
use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use AdvisingApp\Ai\Jobs\PrepareAiThreadCloning;
use AdvisingApp\Ai\Jobs\PrepareAiThreadEmailing;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use AdvisingApp\Ai\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;

/**
 * @property-read array $customAssistants
 * @property-read EloquentCollection $threadsWithoutAFolder
 */
trait CanManageThreads
{
    #[Locked]
    public ?AiThread $thread = null;

    public $assistantSwitcher = null;

    public $assistantSwitcherMobile = null;

    #[Computed]
    public function customAssistants(): array
    {
        return AiAssistant::query()
            ->where('application', static::APPLICATION)
            ->where('is_default', false)
            ->orderBy('name')
            ->withCount('threads')
            ->withCount('upvotes')
            ->get()
            ->mapWithKeys(fn (AiAssistant $assistant) => [
                $assistant->id => view('ai::components.options.assistant', ['assistant' => $assistant])->render(),
            ])
            ->all();
    }

    public function assistantSwitcherForm(Form $form, string $propertyName = 'assistantSwitcher'): Form
    {
        return $form
            ->schema([
                Select::make($propertyName)
                    ->label('Choose an assistant')
                    ->placeholder('Search for an assistant')
                    ->searchPrompt('Search')
                    ->hiddenLabel()
                    ->allowHtml()
                    ->options(fn (): array => $this->customAssistants)
                    ->getSearchResultsUsing(function (string $search): array {
                        return AiAssistant::query()
                            ->where('application', static::APPLICATION)
                            ->where('is_default', false)
                            ->where('name', 'like', "%{$search}%")
                            ->orderBy('name')
                            ->withCount('threads')
                            ->withCount('upvotes')
                            ->get()
                            ->mapWithKeys(fn (AiAssistant $assistant) => [
                                $assistant->id => view('ai::components.options.assistant', ['assistant' => $assistant])->render(),
                            ])
                            ->all();
                    })
                    ->live()
                    ->afterStateUpdated(function ($component, $state) {
                        if (blank($state)) {
                            return;
                        }

                        $this->createThread(
                            AiAssistant::query()
                                ->where('application', static::APPLICATION)
                                ->find($state),
                        );

                        $component->state(null);

                        $this->dispatch('close-assistant-search');
                        $this->dispatch('close-assistant-sidebar');
                    })
                    ->searchable(),
            ]);
    }

    public function assistantSwitcherMobileForm(Form $form): Form
    {
        return $this->assistantSwitcherForm($form, 'assistantSwitcherMobile');
    }

    public function getCanManageThreadsForms(): array
    {
        return [
            'assistantSwitcherForm',
            'assistantSwitcherMobileForm',
        ];
    }

    public function toggleAssistantUpvote(): void
    {
        $this->thread->assistant->toggleUpvote();
    }

    public function createThread(?AiAssistant $assistant = null): void
    {
        if (! $assistant?->exists) {
            // Prevent dependency injection of an empty assistant model.
            $assistant = null;
        }

        $this->thread = app(CreateThread::class)(static::APPLICATION, $assistant);

        if (! is_null($expiredVectorStore = $this->getExpiredVectorStoresForThread($this->thread))) {
            foreach ($expiredVectorStore as $expiredVectorStore) {
                $this->recreateVectorStoreForThread($this->thread, $expiredVectorStore);
            }
        }
    }

    #[Computed]
    public function threadsWithoutAFolder(): EloquentCollection
    {
        return auth()->user()
            ->aiThreads()
            ->whereRelation('assistant', 'application', static::APPLICATION)
            ->whereNotNull('name')
            ->doesntHave('folder')
            ->latest('updated_at')
            ->get();
    }

    public function loadFirstThread(): void
    {
        $this->selectThread($this->threadsWithoutAFolder->first());

        if ($this->thread) {
            if (! is_null($expiredVectorStore = $this->getExpiredVectorStoresForThread($this->thread))) {
                foreach ($expiredVectorStore as $expiredVectorStore) {
                    $this->recreateVectorStoreForThread($this->thread, $expiredVectorStore);
                }
            }

            return;
        }

        $this->createThread();
    }

    public function selectThread(?AiThread $thread): void
    {
        if (! $thread) {
            return;
        }

        if (
            $this->thread &&
            blank($this->thread->name) &&
            (! $this->thread->messages()->exists())
        ) {
            app(DeleteThread::class)($this->thread);
        }

        if (! $thread->user()->is(auth()->user())) {
            abort(404);
        }

        $this->thread = $thread;
    }

    public function getExpiredVectorStoresForThread(): ?array
    {
        $thread = $this->thread->assistant->model->getService()->retrieveThread($this->thread);

        // Currently threads only support a single vector store
        $expiredVectorStores = collect($thread->vectorStoreIds)
            ->map(function ($vectorStoreId) {
                $vectorStoreResponse = $this->thread->assistant->model->getService()->retrieveVectorStore($vectorStoreId);

                if ($vectorStoreResponse->status === 'expired') {
                    return $vectorStoreResponse;
                }

                return null;
            })
            ->filter()
            ->toArray();

        return ! empty($expiredVectorStores) ? $expiredVectorStores : null;
    }

    public function recreateVectorStoreForThread(AiThread $thread, array $vectorStore): void
    {
        $vectorStoreFileIds = [];

        $this->retrieveAllVectorStoreFileIds(
            thread: $thread,
            vectorStoreId: $vectorStore['id'],
            vectorStoreFileIds: $vectorStoreFileIds
        );

        // Create new vector store
        $newVectorStore = $thread->assistant->model->getService()->createVectorStore([
            'file_ids' => $vectorStoreFileIds,
            'name' => 'Refreshed vector store ' . now()->timestamp . ' for thread' . $thread->id,
        ]);

        // Update the thread to use the new vector store.
        $thread->assistant->model->getService()->modifyThread($thread, [
            'tool_resources' => [
                'file_search' => [
                    'vector_store_ids' => [$newVectorStore->id],
                ],
            ],
        ]);

        // Ensure the new vector store has processed all of its files.
        $this->awaitVectorStoreProcessing(
            thread: $thread,
            vectorStore: $newVectorStore
        );
    }

    public function retrieveAllVectorStoreFileIds($thread, $vectorStoreId, &$vectorStoreFileIds = [], $after = null)
    {
        $params = [];

        if ($after !== null) {
            $params['after'] = $after;
        }

        $response = $thread->assistant->model->getService()->retrieveVectorStoreFiles($thread, $vectorStoreId, $params);

        collect($response->data)->each(function ($file) use (&$vectorStoreFileIds) {
            $vectorStoreFileIds[] = $file['id'];
        });

        if ($response->hasMore === true) {
            $this->retrieveAllVectorStoreFileIds(
                thread: $thread,
                vectorStoreId: $vectorStoreId,
                vectorStoreFileIds: $vectorStoreFileIds,
                after: $response->lastId
            );
        }
    }

    public function awaitVectorStoreProcessing(AiThread $thread, VectorStoresDataTransferObject $vectorStore): void
    {
        $timeout = 60;

        $vectorStoreResponseStatus = $vectorStore->status;

        while ($vectorStoreResponseStatus !== 'completed') {
            if ($timeout <= 0) {
                // TODO Throw exception...
            }

            if ($vectorStoreResponseStatus === 'expired') {
                // TODO Something went wrong and we need to restart the process...
            }

            usleep(500000);

            $vectorStoreResponseStatus = $thread->assistant->model->getService()->retrieveVectorStore($vectorStore->id)->status;

            $timeout -= 0.5;
        }
    }

    public function saveThreadAction(): Action
    {
        return Action::make('saveThread')
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
                $this->thread->name = $data['name'];
                $this->thread->save();

                $folder = auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($data['folder']);

                if (! $folder) {
                    unset($this->threadsWithoutAFolder);

                    return;
                }

                $this->moveThread($this->thread, $folder);
                unset($this->folders);
            });
    }

    public function deleteThreadAction(): Action
    {
        return Action::make('deleteThread')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $thread = auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread']);

                if (! $thread) {
                    return;
                }

                $thread->delete();

                if ($thread->is($this->thread)) {
                    $this->createThread();
                }

                unset($this->threadsWithoutAFolder, $this->folders);
            })
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function editThreadAction(): Action
    {
        return Action::make('editThread')
            ->modalSubmitActionLabel('Save')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->fillForm(fn (array $arguments) => [
                'name' => auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread'])
                    ?->name,
            ])
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Rename this chat')
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                $thread = auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread']);

                if (! $thread) {
                    return;
                }

                $thread->name = $data['name'];
                $thread->save();

                unset($this->threadsWithoutAFolder, $this->folders);
            })
            ->icon('heroicon-m-pencil')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function cloneThreadAction(): Action
    {
        return Action::make('cloneThread')
            ->label('Clone')
            ->modalHeading('Clone chat')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('targetType')
                    ->label('To')
                    ->options(AiThreadShareTarget::class)
                    ->enum(AiThreadShareTarget::class)
                    ->default(AiThreadShareTarget::default()->value)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('targetIds', [])),
                Select::make('targetIds')
                    ->label(fn (Get $get): string => match ($get('targetType')) {
                        AiThreadShareTarget::Team->value => 'Select Teams',
                        AiThreadShareTarget::User->value => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('targetType')))
                    ->options(function (Get $get): Collection {
                        return match ($get('targetType')) {
                            AiThreadShareTarget::Team->value => Team::orderBy('name')->pluck('name', 'id'),
                            AiThreadShareTarget::User->value => User::tap(new WithoutSuperAdmin())->whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->rules([fn (Get $get) => match ($get('targetType')) {
                        AiThreadShareTarget::User->value => new RestrictSuperAdmin('clone'),
                        AiThreadShareTarget::Team->value => null,
                    },
                    ]),
            ])
            ->action(function (array $arguments, array $data) {
                $thread = auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread']);

                if (! $thread) {
                    return;
                }

                dispatch(new PrepareAiThreadCloning($thread, $data['targetType'], $data['targetIds'], auth()->user()));
            })
            ->link()
            ->icon('heroicon-m-document-duplicate')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }

    public function emailThreadAction(): Action
    {
        return Action::make('emailThread')
            ->label('Email')
            ->modalHeading('Email chat')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('targetType')
                    ->label('To')
                    ->options(AiThreadShareTarget::class)
                    ->enum(AiThreadShareTarget::class)
                    ->default(AiThreadShareTarget::default()->value)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('targetIds', [])),
                Select::make('targetIds')
                    ->label(fn (Get $get): string => match ($get('targetType')) {
                        AiThreadShareTarget::Team->value => 'Select Teams',
                        AiThreadShareTarget::User->value => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('targetType')))
                    ->options(function (Get $get): Collection {
                        return match ($get('targetType')) {
                            AiThreadShareTarget::Team->value => Team::orderBy('name')->pluck('name', 'id'),
                            AiThreadShareTarget::User->value => User::tap(new WithoutSuperAdmin())->orderBy('name')->pluck('name', 'id'),
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->rules([fn (Get $get) => match ($get('targetType')) {
                        AiThreadShareTarget::User->value => new RestrictSuperAdmin('email'),
                        AiThreadShareTarget::Team->value => null,
                    },
                    ]),
            ])
            ->action(function (array $arguments, array $data) {
                $thread = auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread']);

                if (! $thread) {
                    return;
                }

                dispatch(new PrepareAiThreadEmailing($thread, $data['targetType'], $data['targetIds'], auth()->user()));
            })
            ->link()
            ->icon('heroicon-m-envelope')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }

    #[Renderless]
    public function isThreadLocked(): bool
    {
        if (! $this->thread) {
            return false;
        }

        return $this->thread->locked_at !== null;
    }
}
