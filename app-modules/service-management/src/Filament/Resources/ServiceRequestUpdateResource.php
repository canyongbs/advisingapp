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

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use App\Filament\Tables\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AdvisingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\ViewServiceRequestUpdate;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\ListServiceRequestUpdates;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate;

class ServiceRequestUpdateResource extends Resource
{
    protected static ?string $model = ServiceRequestUpdate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('service_request_id')
                    ->relationship('serviceRequest', 'id')
                    ->preload()
                    ->label('Service Request')
                    ->required()
                    ->exists(
                        table: (new ServiceRequest())->getTable(),
                        column: (new ServiceRequest())->getKeyName()
                    ),
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Select::make('direction')
                    ->options(ServiceRequestUpdateDirection::class)
                    ->label('Direction')
                    ->required()
                    ->enum(ServiceRequestUpdateDirection::class)
                    ->default(ServiceRequestUpdateDirection::default()),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                Tables\Columns\TextColumn::make('serviceRequest.respondent.full')
                    ->label('Related To')
                    ->sortable(query: function (Builder $query, string $direction, $record): Builder {
                        // TODO: Update this to work with other respondent types
                        return $query->join('service_requests', 'service_request_updates.service_request_id', '=', 'service_requests.id')
                            ->join('students', function ($join) {
                                $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                    ->where('service_requests.respondent_type', '=', 'student');
                            })
                            ->orderBy('full', $direction);
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('serviceRequest.respondent.sisid')
                    ->label('SIS ID')
                    ->sortable(query: function (Builder $query, string $direction, $record): Builder {
                        // TODO: Update this to work with other respondent types
                        return $query->join('service_requests', 'service_request_updates.service_request_id', '=', 'service_requests.id')
                            ->join('students', function ($join) {
                                $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                    ->where('service_requests.respondent_type', '=', 'student');
                            })
                            ->orderBy('sisid', $direction);
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('serviceRequest.respondent.otherid')
                    ->label('Other ID')
                    ->sortable(query: function (Builder $query, string $direction, $record): Builder {
                        // TODO: Update this to work with other respondent types
                        return $query->join('service_requests', 'service_request_updates.service_request_id', '=', 'service_requests.id')
                            ->join('students', function ($join) {
                                $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                    ->where('service_requests.respondent_type', '=', 'student');
                            })
                            ->orderBy('otherid', $direction);
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('serviceRequest.service_request_number')
                    ->label('Service Request')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('internal')
                    ->boolean()
                    ->label('Internal'),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => $state->getLabel())
                    ->icon(fn (ServiceRequestUpdateDirection $state): string => $state->getIcon()),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('internal')
                    ->label('Internal'),
                Tables\Filters\SelectFilter::make('direction')
                    ->label('Direction')
                    ->options(ServiceRequestUpdateDirection::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequestUpdates::route('/'),
            'create' => CreateServiceRequestUpdate::route('/create'),
            'view' => ViewServiceRequestUpdate::route('/{record}'),
            'edit' => EditServiceRequestUpdate::route('/{record}/edit'),
        ];
    }
}
