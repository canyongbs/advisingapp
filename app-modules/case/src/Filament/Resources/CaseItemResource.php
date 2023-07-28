<?php

namespace Assist\Case\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\Case\Models\CaseItem;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Assist\Case\Models\CaseItemStatus;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\EditCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\ViewCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\ListCaseItems;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\CreateCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\CreatedByRelationManager;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\AssignedToRelationManager;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\RespondentRelationManager;

class CaseItemResource extends Resource
{
    protected static ?string $model = CaseItem::class;

    protected static ?string $navigationGroup = 'Cases';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $label = 'Case';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled(),
                TextInput::make('casenumber')
                    ->label('Case #')
                    ->required()
                    ->disabledOn('edit'),
                Select::make('institution')
                    ->relationship('institution', 'name')
                    ->label('Institution')
                    ->required(),
                Select::make('state')
                    ->options(CaseItemStatus::pluck('name', 'id'))
                    ->relationship('state', 'name')
                    ->label('State')
                    ->required(),
                Select::make('priority')
                    ->relationship(
                        name: 'priority',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                    )
                    ->label('Priority')
                    ->required(),
                Textarea::make('close_details')
                    ->label('Close Details/Description')
                    ->nullable(),
                Textarea::make('res_details')
                    ->label('Internal Case Details')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('casenumber')
                    ->label('Case #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('respondent.full')
                    ->label('Student')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // TODO: Look into issues with the Power Joins package being able to handle this
                        //ray($query->joinRelationship('respondent', [
                        //    'respondent' => [
                        //        'students' => function ($join) {
                        //            // ...
                        //        },
                        //    ],
                        //])->toSql());

                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('full', $direction);
                    }),
                Tables\Columns\TextColumn::make('respondent.sisid')
                    ->label('SIS ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('sisid', $direction);
                    }),
                Tables\Columns\TextColumn::make('respondent.otherid')
                    ->label('Other ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('otherid', $direction);
                    }),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Institution')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // TODO: Figure out how to get this to display a list of existing items rather than a search
                Tables\Filters\SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->multiple()
                    ->preload(),
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
        return [
            RespondentRelationManager::class,
            RelationGroup::make('Related Users', [
                AssignedToRelationManager::class,
                CreatedByRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseItems::route('/'),
            'create' => CreateCaseItem::route('/create'),
            'view' => ViewCaseItem::route('/{record}'),
            'edit' => EditCaseItem::route('/{record}/edit'),
        ];
    }
}
