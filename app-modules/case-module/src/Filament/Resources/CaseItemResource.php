<?php

namespace Assist\CaseModule\Filament\Resources;

use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Assist\CaseModule\Models\CaseItem;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Assist\CaseModule\Models\CaseItemStatus;
use Filament\Forms\Components\MorphToSelect;
use Assist\CaseModule\Filament\Resources\CaseItemResource\Pages\EditCaseItem;
use Assist\CaseModule\Filament\Resources\CaseItemResource\Pages\ViewCaseItem;
use Assist\CaseModule\Filament\Resources\CaseItemResource\Pages\ListCaseItems;
use Assist\CaseModule\Filament\Resources\CaseItemResource\Pages\CreateCaseItem;

class CaseItemResource extends Resource
{
    protected static ?string $model = CaseItem::class;

    protected static ?string $navigationGroup = 'Cases';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('casenumber')
                    ->label('Case #')
                    ->required()
                    ->disabledOn('edit'),
                MorphToSelect::make('respondent')
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->getOptionLabelFromRecordUsing(fn (Student $student): string => "{$student->first_name} {$student->middle_name} {$student->last_name}")
                            ->titleAttribute('first_name'),
                    ])
                    ->searchable()
                    ->label('Respondent'),
                // TODO: Add Institution input
                Select::make('state')
                    ->options(CaseItemStatus::pluck('name', 'id'))
                    ->relationship('state', 'name')
                    ->label('State')
                    ->required(),
                // TODO: Add Type input
                Select::make('priority')
                    ->relationship(
                        relationshipName: 'priority',
                        titleAttribute: 'name',
                        modifyOptionsQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                    )
                    ->label('Priority')
                    ->required(),
                Select::make('assignedTo')
                    ->relationship(
                        relationshipName: 'assignedTo',
                        titleAttribute: 'name',
                    )
                    ->label('Assigned To')
                    ->required()
                    ->searchable(['name', 'email']),
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
                Tables\Columns\TextColumn::make('casenumber')
                    ->label('Case #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority.name')
                    ->label('Priority')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByPowerJoins('priority.order', $direction);
                    }),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (CaseItem $caseItem) => $caseItem->state->color),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->multiple(),
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
