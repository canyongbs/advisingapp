<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Institution;
use Assist\Case\Models\CaseItemType;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Assist\Case\Models\CaseItemStatus;
use Filament\Forms\Components\Textarea;
use Assist\Case\Models\CaseItemPriority;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Assist\Case\Filament\Resources\CaseItemResource;

class EditCaseItem extends EditRecord
{
    protected static string $resource = CaseItemResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)->schema([
            TextInput::make('id')
                ->disabled(),
            TextInput::make('casenumber')
                ->label('Case #')
                ->disabled(),
            Select::make('institution')
                ->relationship('institution', 'name')
                ->label('Institution')
                ->required()
                ->exists((new Institution())->getTable(), 'id'),
            Select::make('status')
                ->relationship('status', 'name')
                ->preload()
                ->label('Status')
                ->required()
                ->exists((new CaseItemStatus())->getTable(), 'id'),
            Select::make('priority')
                ->relationship(
                    name: 'priority',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                )
                ->label('Priority')
                ->required()
                ->exists((new CaseItemPriority())->getTable(), 'id'),
            Select::make('type')
                ->relationship('type', 'name')
                ->preload()
                ->label('Type')
                ->required()
                ->exists((new CaseItemType())->getTable(), 'id'),
            Textarea::make('close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make('res_details')
                ->label('Internal Case Details')
                ->nullable()
                ->string(),
            MorphToSelect::make('respondent')
                ->label('Respondent')
                ->searchable()
                ->preload()
                ->types([
                    MorphToSelect\Type::make(Student::class)
                        ->titleAttribute('full'),
                    MorphToSelect\Type::make(Prospect::class)
                        ->titleAttribute('full'),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
