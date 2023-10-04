<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\ViewServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\ListServiceRequestUpdates;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate;

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
                    ->translateLabel()
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
                    ->options(collect(ServiceRequestUpdateDirection::cases())->mapWithKeys(fn (ServiceRequestUpdateDirection $direction) => [$direction->value => $direction->name]))
                    ->label('Direction')
                    ->required()
                    ->enum(ServiceRequestUpdateDirection::class),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
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
                    ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => Str::ucfirst($state->value))
                    ->icon(fn (ServiceRequestUpdateDirection $state): string => match ($state) {
                        ServiceRequestUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
                        ServiceRequestUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('internal')
                    ->label('Internal')
                    ->translateLabel(),
                Tables\Filters\SelectFilter::make('direction')
                    ->label('Direction')
                    ->translateLabel()
                    ->options(
                        collect(ServiceRequestUpdateDirection::cases())
                            ->mapWithKeys(fn (ServiceRequestUpdateDirection $direction) => [$direction->value => $direction->name])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
