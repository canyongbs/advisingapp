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

use AdvisingApp\Ai\Actions\CreateThread;
use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use AdvisingApp\Ai\Jobs\PrepareAiThreadCloning;
use AdvisingApp\Ai\Jobs\PrepareAiThreadEmailing;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Rules\RestrictSuperAdmin;
use AdvisingApp\Ai\Services\Contracts\AiServiceLifecycleHooks;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Team\Models\Team;
use App\Models\Scopes\WithoutSuperAdmin;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;

/**
 * @property-read array $customAssistants
 */
trait CanManageThreads
{
    #[Locked]
    public ?AiThread $thread = null;

    public $assistantSwitcher = null;

    public $assistantSwitcherMobile = null;

    #[Locked]
    public array $threadsWithoutAFolder = [];

    public function mountCanManageThreads(): void
    {
        $this->threadsWithoutAFolder = $this->getThreadsWithoutAFolder();
    }

    #[Computed]
    public function customAssistants(): array
    {
        return AiAssistant::query()
            ->where('application', static::APPLICATION)
            ->whereNull('archived_at')
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
                            ->whereNull('archived_at')
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
                                ->whereNull('archived_at')
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
    }

    public function getThreadsWithoutAFolder(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->aiThreads()
            ->withMax('messages', 'created_at')
            ->whereRelation('assistant', 'application', static::APPLICATION)
            ->whereNotNull('name')
            ->doesntHave('folder')
            ->latest('updated_at')
            ->get()
            ->each->append('last_engaged_at')
            ->toArray();
    }

    public function loadFirstThread(): void
    {
        $this->selectThread(collect($this->threadsWithoutAFolder)->whereNull('assistant.archived_at')->first());

        if ($this->thread) {
            $service = $this->thread->assistant->model->getService();

            if ($service instanceof AiServiceLifecycleHooks) {
                $service->afterLoadFirstThread($this->thread);
            }

            return;
        }

        $this->createThread();
    }

    public function selectThread(?array $thread): void
    {
        if (! $thread) {
            return;
        }

        $thread = AiThread::find($thread['id']);

        if (
            $this->thread &&
            blank($this->thread->name) &&
            (! $this->thread->messages()->exists())
        ) {
            $this->thread->delete();
        }

        if (! $thread->user()->is(auth()->user())) {
            abort(404);
        }

        $this->thread = $thread;

        $service = $this->thread->assistant->model->getService();

        if ($service instanceof AiServiceLifecycleHooks) {
            $service->afterThreadSelected($this->thread);
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

                $this->thread->saved_at = now();

                $this->thread->save();

                dispatch(new RecordTrackedEvent(
                    type: TrackedEventType::AiThreadSaved,
                    occurredAt: now(),
                ));

                $folder = auth()->user()->aiThreadFolders()
                    ->where('application', static::APPLICATION)
                    ->find($data['folder']);

                if (! $folder) {
                    $this->threadsWithoutAFolder = $this->getThreadsWithoutAFolder();

                    return;
                }

                $this->moveThread($this->thread, $folder);
                $this->folders = $this->getFolders();
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

                $this->threadsWithoutAFolder = $this->getThreadsWithoutAFolder();
                $this->folders = $this->getFolders();
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

                $this->threadsWithoutAFolder = $this->getThreadsWithoutAFolder();
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

                dispatch(new PrepareAiThreadCloning($thread, AiThreadShareTarget::parse($data['targetType']), $data['targetIds'], auth()->user()));
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
