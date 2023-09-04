<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Forms\Form;
use App\Models\Institution;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Assist\Case\Models\CaseItemStatus;
use Filament\Forms\Components\Textarea;
use Assist\Case\Models\CaseItemPriority;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Assist\Case\Models\ServiceRequestType;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Assist\Case\Filament\Resources\CaseItemResource;

class CreateCaseItem extends CreateRecord
{
    protected static string $resource = CaseItemResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('casenumber')
                    ->label('Case #')
                    ->required()
                    ->unique(),
                Select::make('institution_id')
                    ->relationship('institution', 'name')
                    ->label('Institution')
                    ->required()
                    ->exists((new Institution())->getTable(), 'id'),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->preload()
                    ->label('Status')
                    ->required()
                    ->exists((new CaseItemStatus())->getTable(), 'id'),
                Select::make('priority_id')
                    ->relationship(
                        name: 'priority',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                    )
                    ->label('Priority')
                    ->required()
                    ->exists((new CaseItemPriority())->getTable(), 'id'),
                Select::make('type_id')
                    ->relationship('type', 'name')
                    ->preload()
                    ->label('Type')
                    ->required()
                    ->exists((new ServiceRequestType())->getTable(), 'id'),
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
}
