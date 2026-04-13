<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
      same in return. Canyon GBS® and Advising App® are registered trademarks of
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

namespace AdvisingApp\MeetingCenter\Filament\Pages;

use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Actions\ResolveEducatableFromEmail;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\Feature;
use App\Filament\Clusters\GroupAppointments;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;

/** @property Schema $form */
class SharedCalendar extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?int $navigationSort = 10;

    protected string $view = 'meeting-center::filament.pages.shared-calendar';

    protected static ?string $cluster = GroupAppointments::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationLabel = 'Shared Calendar';

    #[Url(as: 'view')]
    public string $viewType = 'table';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'groupFilter' => 'my_groups',
            'selectedGroupIds' => [],
            'hidePast' => true,
        ]);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return $user->can(['group_appointment.view-any']) && Gate::check(Feature::ScheduleAndAppointments->getGateName());
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Advanced Filtering')
                    ->schema([
                        Select::make('groupFilter')
                            ->label('Groups')
                            ->options([
                                'my_groups' => 'My Groups',
                                'selected' => 'Select Groups',
                            ])
                            ->selectablePlaceholder(false)
                            ->live()
                            ->afterStateUpdated(fn () => $this->dispatchCalendarRefresh()),
                        Select::make('selectedGroupIds')
                            ->label('Select Groups')
                            ->multiple()
                            ->options(fn (): array => BookingGroup::orderBy('name')->pluck('name', 'id')->toArray())
                            ->visible(fn (): bool => ($this->data['groupFilter'] ?? 'my_groups') === 'selected')
                            ->live()
                            ->afterStateUpdated(fn () => $this->dispatchCalendarRefresh()),
                        Checkbox::make('hidePast')
                            ->label('Hide Past')
                            ->columnStart(1)
                            ->live()
                            ->afterStateUpdated(fn () => $this->dispatchCalendarRefresh()),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = BookingGroupAppointment::query();

                $groupFilter = $this->data['groupFilter'] ?? 'my_groups';
                $selectedGroupIds = $this->data['selectedGroupIds'] ?? [];
                $hidePast = $this->data['hidePast'] ?? true;

                if ($hidePast) {
                    $query->where('starts_at', '>=', now()->startOfDay());
                }

                if ($groupFilter === 'my_groups') {
                    $user = auth()->user();
                    assert($user instanceof User);

                    $query->whereHas('bookingGroup', function (Builder $query) use ($user): void {
                        $query->whereHas('users', fn (Builder $query) => $query->where('users.id', $user->id));

                        if ($user->team_id) {
                            $query->orWhereHas('teams', fn (Builder $query) => $query->where('teams.id', $user->team_id));
                        }
                    });
                } elseif (! empty($selectedGroupIds)) {
                    $query->whereIn('booking_group_id', $selectedGroupIds);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->state(function (BookingGroupAppointment $record): string {
                        $educatable = app(ResolveEducatableFromEmail::class)($record->email);

                        if ($educatable instanceof Student) {
                            return "{$record->name} (Student)";
                        }

                        if ($educatable instanceof Prospect) {
                            return "{$record->name} (Prospect)";
                        }

                        return $record->name;
                    })
                    ->url(function (BookingGroupAppointment $record): ?string {
                        $educatable = app(ResolveEducatableFromEmail::class)($record->email);

                        if ($educatable instanceof Student) {
                            return StudentResource::getUrl('view', ['record' => $educatable->sisid]);
                        }

                        if ($educatable instanceof Prospect) {
                            return ProspectResource::getUrl('view', ['record' => $educatable]);
                        }

                        return null;
                    }),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('bookingGroup.name')
                    ->label('Group')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Ends At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(function (BookingGroupAppointment $record): string {
                        $minutes = $record->starts_at->diffInMinutes($record->ends_at);
                        $hours = intdiv(intval($minutes), 60);
                        $remainingMinutes = $minutes % 60;

                        if ($hours > 0 && $remainingMinutes > 0) {
                            return "{$hours}h {$remainingMinutes}m";
                        }

                        if ($hours > 0) {
                            return "{$hours}h";
                        }

                        return "{$remainingMinutes}m";
                    }),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    protected function dispatchCalendarRefresh(): void
    {
        $this->dispatch(
            'refresh-group-events',
            groupFilter: $this->data['groupFilter'] ?? 'my_groups',
            selectedGroupIds: $this->data['selectedGroupIds'] ?? [],
            hidePast: $this->data['hidePast'] ?? true,
        );
    }
}
