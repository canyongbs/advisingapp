<?php

namespace App\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CaseloadResource;
use Filament\Tables\Concerns\InteractsWithTable;

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
                Select::make('model')
                    ->options([
                        'student' => 'Student',
                        'prospect' => 'Prospect',
                    ])
                    ->default('student')
                    ->live(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
            ])
            ->filters([
                Filter::make('sap')
                    ->query(fn (Builder $query) => $query->where('sap', true)),
            ])
            ->query(fn () => match ($this->data['model']) {
                'student' => Student::query(),
                'prospect' => Prospect::query(),
                default => null,
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
