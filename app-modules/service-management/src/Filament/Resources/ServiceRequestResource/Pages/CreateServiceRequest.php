<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Forms\Form;
use App\Models\Institution;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
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
                    ->exists((new ServiceRequestStatus())->getTable(), 'id'),
                Select::make('priority_id')
                    ->relationship(
                        name: 'priority',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                    )
                    ->label('Priority')
                    ->required()
                    ->exists((new ServiceRequestPriority())->getTable(), 'id'),
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
                    ->label('Internal Service Request Details')
                    ->nullable()
                    ->string(),
                MorphToSelect::make('respondent')
                    ->label('Related To')
                    ->searchable()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
            ]);
    }
}
