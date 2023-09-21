<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
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
                TextInput::make('name')
                    ->autocomplete(false)
                    ->string()
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(CaseloadType::class)
                    ->default(CaseloadType::default())
                    ->disableOptionWhen(fn ($value) => CaseloadType::from($value)->disabled())
                    ->selectablePlaceholder(false)
                    ->required(),
                Select::make('model')
                    ->label('Population')
                    ->options(CaseloadModel::class)
                    ->required()
                    ->default(CaseloadModel::default())
                    ->selectablePlaceholder(false)
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->cacheForms();
                        $this->bootedInteractsWithTable();
                        $this->resetTableFiltersForm();
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(CaseloadResource::columns($this->data['model']))
            ->filters(CaseloadResource::filters($this->data['model']))
            ->query(fn () => $this->data['model']->query());
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->data['type'] === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } elseif ($this->data['type'] === CaseloadType::Static) {
            //$this->getFilteredTableQuery()->pluck('id');
            //bulk insert
        }

        return $data;
    }
}
