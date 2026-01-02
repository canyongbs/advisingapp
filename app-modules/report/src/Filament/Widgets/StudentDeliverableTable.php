<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class StudentDeliverableTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Student Engagement Deliberability';

    protected int | string | array $columnSpan = 'full';

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget() {}

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        return $table
            ->query(
                Student::select('sisid', 'full_name', 'primary_email_id', 'primary_phone_id')
                    ->when(
                        $startDate && $endDate,
                        function (Builder $query) use ($startDate, $endDate): Builder {
                            return $query->whereBetween('created_at_source', [$startDate, $endDate]);
                        },
                    )
                    ->when(
                        $groupId,
                        fn (Builder $query) => $this->groupFilter($query, $groupId)
                    )
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Primary Email')
                    ->searchable(),
                TextColumn::make('email_bounce')
                    ->label('Email Status')
                    ->badge()
                    ->color(fn (Student $record) => ($record->primaryEmailAddress?->bounced()->exists()) ? 'warning' : 'info')
                    ->state(fn (Student $record) => ($record->primaryEmailAddress?->bounced()->exists()) ? 'Bounced' : 'Healthy'),
                TextColumn::make('primaryPhoneNumber.number')
                    ->label('Primary Phone')
                    ->searchable(),
                TextColumn::make('sms_opt_out')
                    ->label('Phone Status')
                    ->badge()
                    ->color(fn (Student $record) => ($record->primaryPhoneNumber?->smsOptOut()->exists()) ? 'warning' : 'info')
                    ->state(fn (Student $record) => ($record->primaryPhoneNumber?->smsOptOut()->exists()) ? 'Opt Out' : 'Healthy'),
            ])
            ->filters([
                SelectFilter::make('email_status')
                    ->label('Email Status')
                    ->options([
                        'bounced' => 'Bounced',
                        'healthy' => 'Healthy',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'bounced') {
                            return $query->whereHas('primaryEmailAddress.bounced');
                        }

                        if (($data['value'] ?? null) === 'healthy') {
                            return $query->whereDoesntHave('primaryEmailAddress.bounced');
                        }

                        return $query;
                    }),
                SelectFilter::make('phone_status')
                    ->label('Phone Status')
                    ->options([
                        'opt-out' => 'Opt Out',
                        'healthy' => 'Healthy',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'opt-out') {
                            return $query->whereHas('primaryPhoneNumber.smsOptOut');
                        }

                        if (($data['value'] ?? null) === 'healthy') {
                            return $query->whereDoesntHave('primaryPhoneNumber.smsOptOut');
                        }

                        return $query;
                    }),
            ])
            ->paginationMode(PaginationMode::Default);
    }
}
