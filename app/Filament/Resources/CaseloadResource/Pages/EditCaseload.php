<?php

namespace App\Filament\Resources\CaseloadResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use App\Filament\Resources\CaseloadResource;
use Filament\Tables\Concerns\InteractsWithTable;

class EditCaseload extends EditRecord implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }

    protected static string $resource = CaseloadResource::class;

    protected static string $view = 'filament.resources.caseloads.pages.edit-caseload';

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
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
            ])
            ->filters([
                //hide filters if static
                Filter::make('sap')
                    ->query(fn (Builder $query) => $query->where('sap', true)),
            ])
            ->query(fn () => match ($this->data['model']) {
                //static pass in keys wherekey
                'student' => Student::query(),
                'prospect' => Prospect::query(),
                default => null,
            });
    }

    public function bootedInteractsWithTable(): void
    {
        if ($this->shouldMountInteractsWithTable) {
            $this->tableFilters = $this->getRecord()->filters;
        }
        $this->baseBootedInteractsWithTable();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        //check type
        $data['filters'] = $this->tableFilters ?? [];

        ray($data);

        return $data;
    }
}
