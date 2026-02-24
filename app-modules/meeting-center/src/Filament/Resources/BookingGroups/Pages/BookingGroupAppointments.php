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

namespace AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\BookingGroupResource;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Actions\ResolveEducatableFromEmail;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\GroupBookingFeature;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingGroupAppointments extends ManageRelatedRecords
{
    protected static string $resource = BookingGroupResource::class;

    protected static string $relationship = 'bookingGroupAppointments';

    protected static ?string $navigationLabel = 'Appointments';

    protected static ?string $breadcrumb = 'Appointments';

    protected static ?string $title = 'Appointments';

    protected static ?int $navigationSort = 20;

    public static function canAccess(array $parameters = []): bool
    {
        return GroupBookingFeature::active();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
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
}
