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

use AdvisingApp\Report\Filament\Exports\EmailPhoneHealthExporter;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryEmailAddress;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
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

    protected static ?string $heading = 'Email and Phone Health Detail';

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
                    ->with(['primaryEmailAddress', 'primaryPhoneNumber'])
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
                TextColumn::make('email_status')
                    ->label('Email Status')
                    ->badge()
                    ->color(fn (Student $record) => $record->canReceiveEmail() ? 'info' : 'warning')
                    ->state(fn (Student $record) => $record->canReceiveEmail() ? 'Healthy' : 'Unhealthy'),
                IconColumn::make('primary_email_set')
                    ->label('Primary Email Set')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => filled($record->primaryEmailAddress)),
                IconColumn::make('email_bounced')
                    ->label('Email Bounce')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => $record->primaryEmailAddress ? ($record->primaryEmailAddress->bounced !== null) : false),
                IconColumn::make('email_opt_out')
                    ->label('Email Opt Out')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => $record->primaryEmailAddress ? ($record->primaryEmailAddress->optedOut?->status === EmailAddressOptInOptOutStatus::OptedOut) : false),
                TextColumn::make('phone_status')
                    ->label('Phone Status')
                    ->badge()
                    ->color(fn (Student $record) => $record->canReceiveSms() ? 'info' : 'warning')
                    ->state(fn (Student $record) => $record->canReceiveSms() ? 'Healthy' : 'Unhealthy'),
                IconColumn::make('primary_phone_set')
                    ->label('Primary Phone Set')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => filled($record->primaryPhoneNumber)),
                IconColumn::make('can_receive_sms')
                    ->label('SMS Capable')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => filled($record->primaryPhoneNumber?->number) && $record->primaryPhoneNumber->can_receive_sms),
                IconColumn::make('smsOptOut')
                    ->label('SMS Opt Out')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn (Student $record) => $record->primaryPhoneNumber ? $record->primaryPhoneNumber->smsOptOut()->exists() : false),
            ])
            ->filters([
                SelectFilter::make('email_status')
                    ->label('Email Status')
                    ->options([
                        'unhealthy' => 'Unhealthy',
                        'healthy' => 'Healthy',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        /** @var Builder<Student> $query */
                        if (($data['value'] ?? null) === 'unhealthy') {
                            return $query->tap(new UnhealthyEducatablePrimaryEmailAddress());
                        }

                        if (($data['value'] ?? null) === 'healthy') {
                            return $query->whereHas(
                                'primaryEmailAddress',
                                fn (Builder $email) => $email->whereDoesntHave('bounced')
                                    ->whereDoesntHave(
                                        'optedOut',
                                        fn (Builder $optedOut) => $optedOut->where(
                                            'status',
                                            EmailAddressOptInOptOutStatus::OptedOut
                                        )
                                    )
                            );
                        }

                        return $query;
                    }),
                SelectFilter::make('phone_status')
                    ->label('Phone Status')
                    ->options([
                        'unhealthy' => 'Unhealthy',
                        'healthy' => 'Healthy',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'unhealthy') {
                            return $query->tap(new UnhealthyEducatablePrimaryPhoneNumber());
                        }

                        if (($data['value'] ?? null) === 'healthy') {
                            return $query->whereHas('primaryPhoneNumber', function (Builder $query1) {
                                $query1->where(function (Builder $query2) {
                                    $query2->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut');
                                });
                            });
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Go to Student')
                    ->url(fn (Student $record): string => StudentResource::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true)
                    ->icon('heroicon-m-arrow-top-right-on-square'),
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->exporter(EmailPhoneHealthExporter::class),
            ])
            ->paginationMode(PaginationMode::Default);
    }
}
