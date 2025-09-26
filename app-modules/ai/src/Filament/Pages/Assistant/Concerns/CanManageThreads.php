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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use AdvisingApp\Ai\Actions\CreateThread;
use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use AdvisingApp\Ai\Jobs\Advisors\PrepareAiThreadCloning;
use AdvisingApp\Ai\Jobs\Advisors\PrepareAiThreadEmailing;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Rules\RestrictSuperAdmin;
use AdvisingApp\Team\Models\Team;
use App\Models\Scopes\WithoutSuperAdmin;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    public ?string $selectedThreadId = null;

    /**
     * @phpstan-ignore-next-line
     * This doesn't seem to be used at this point.
     */
    public $assistantSwitcher = null;

    /**
     * @phpstan-ignore-next-line
     * This doesn't seem to be used at this point.
     */
    public $assistantSwitcherMobile = null;

    #[Locked]
    public array $threadsWithoutAFolder = [];

    public function mount(): void
    {
        if (request()->thread) {
            if (! Str::isUuid(request()->thread)) {
                $this->dispatch('remove-thread-param');
            } else {
                $aiThread = AiThread::where('id', request()->thread)->where('user_id', auth()->id())->first()?->toArray();

                if ($aiThread) {
                    $this->selectThread($aiThread);
                } else {
                    $this->dispatch('remove-thread-param');
                }
            }
        }
    }

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

    public function assistantSwitcherForm(Schema $schema, string $propertyName = 'assistantSwitcher'): Schema
    {
        return $schema
            ->components([
                Select::make($propertyName)
                    ->label('Choose an assistant')
                    ->placeholder('Search for an advisor')
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

    public function assistantSwitcherMobileForm(Schema $schema): Schema
    {
        return $this->assistantSwitcherForm($schema, 'assistantSwitcherMobile');
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
        $this->selectedThreadId = $this->thread->getKey();
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
        $this->selectedThreadId = $this->thread->getKey();
    }

    public function deleteThreadAction(): Action
    {
        return Action::make('deleteThread')
            ->size(Size::ExtraSmall)
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
            ->size(Size::ExtraSmall)
            ->fillForm(fn (array $arguments) => [
                'name' => auth()->user()->aiThreads()
                    ->whereRelation('assistant', 'application', static::APPLICATION)
                    ->find($arguments['thread'])
                    ?->name,
            ])
            ->schema([
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
            ->modalSubmitAction(fn (Action $action) => $action->color('primary'))
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
            ->schema([
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
                        default => throw new Exception('Target type is not valid.')
                    })
                    ->visible(fn (Get $get): bool => filled($get('targetType')))
                    ->options(function (Get $get): Collection {
                        return match ($get('targetType')) {
                            AiThreadShareTarget::Team->value => Team::orderBy('name')->pluck('name', 'id'),
                            AiThreadShareTarget::User->value => User::tap(new WithoutSuperAdmin())->whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
                            default => throw new Exception('Target type is not valid.')
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->rules([
                        fn (Get $get) => match ($get('targetType')) {
                            AiThreadShareTarget::User->value => new RestrictSuperAdmin('clone'),
                            AiThreadShareTarget::Team->value => null,
                            default => throw new Exception('Target type not valid.')
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
            ->modalSubmitAction(fn (Action $action) => $action->color('primary'));
    }

    public function emailThreadAction(): Action
    {
        return Action::make('emailThread')
            ->label('Email')
            ->modalHeading('Email chat')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->schema([
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
                        default => throw new Exception('Target type is not valid.')
                    })
                    ->visible(fn (Get $get): bool => filled($get('targetType')))
                    ->options(function (Get $get): Collection {
                        return match ($get('targetType')) {
                            AiThreadShareTarget::Team->value => Team::orderBy('name')->pluck('name', 'id'),
                            AiThreadShareTarget::User->value => User::tap(new WithoutSuperAdmin())->orderBy('name')->pluck('name', 'id'),
                            default => throw new Exception('Target type is not valid.')
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->rules([
                        fn (Get $get) => match ($get('targetType')) {
                            AiThreadShareTarget::User->value => new RestrictSuperAdmin('email'),
                            AiThreadShareTarget::Team->value => null,
                            default => throw new Exception('Target type is not valid.')
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
            ->modalSubmitAction(fn (Action $action) => $action->color('primary'));
    }

    #[Renderless]
    public function isThreadLocked(): bool
    {
        if (! $this->thread) {
            return false;
        }

        return $this->thread->locked_at !== null;
    }

    public function refreshThreads(): void
    {
        $this->threadsWithoutAFolder = $this->getThreadsWithoutAFolder();
        $this->folders = $this->getFolders();
    }
}
