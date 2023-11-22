<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Filament\Widgets\TableWidget as BaseWidget;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\AssistDataModel\Models\Scopes\EducatableSort;
use Assist\AssistDataModel\Models\Scopes\EducatableSearch;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

class MyServiceRequests extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('My Service Requests')
            ->query(
                auth()->user()
                    ->serviceRequests()
                    ->getQuery()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                IdColumn::make(),
                TextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('respondent.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->{$record->respondent::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'respondent', search: $search)))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->tap(new EducatableSort($direction))),
                TextColumn::make('respondent.sisid')
                    ->label('SIS ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
                        })->orderBy('sisid', $direction);
                    }),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl(name: 'view', parameters: ['record' => $record])),
            ])
            ->recordUrl(
                fn (ServiceRequest $record): string => ServiceRequestResource::getUrl(name: 'view', parameters: ['record' => $record]),
            )
            ->paginated([5]);
    }
}
