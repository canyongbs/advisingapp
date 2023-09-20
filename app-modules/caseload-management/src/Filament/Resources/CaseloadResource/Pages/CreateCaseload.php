<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class CreateCaseload extends CreateRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CaseloadResource::class;

    protected static string $view = 'filament.resources.caseloads.pages.create-caseload';

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name'),
                Select::make('type')
                    ->options(CaseloadType::class),
                Select::make('model')
                    ->options([
                        'student' => 'Student',
                        'prospect' => 'Prospect',
                    ])
                    ->default('student')
                    ->selectablePlaceholder(false)
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->tableFilters = null;
                        $this->shouldMountInteractsWithTable = true;
                        $this->bootedInteractsWithTable();
                        $this->resetTableFiltersForm();
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
            ])
            ->filters(CaseloadResource::filters($this->data['model']))
            ->query(fn () => match ($this->data['model']) {
                'student' => Student::query(),
                'prospect' => Prospect::query(),
            });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //dyanmic
        $data['filters'] = $this->tableFilters ?? [];

        //static
        //$this->getFilteredTableQuery()->pluck('id');
        //bulk insert

        ray($data);

        return $data;
    }
}
