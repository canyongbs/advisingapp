<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Resources\Pages\CreateRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Models\CaseloadSubject;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class CreateCaseload extends CreateRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CaseloadResource::class;

    protected static string $view = 'caseload-management::filament.resources.caseloads.pages.create-caseload';

    public function form(Form $form): Form
    {
        return $form
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
            ->filters(CaseloadResource::filters($this->data['model']), layout: FiltersLayout::AboveContent)
            // ->actions(CaseloadResource::actions($this->data['model']))
            ->query(fn () => $this->data['model']->query());
    }

    public function afterCreate(): void
    {
        if ($this->data['type'] === CaseloadType::Static) {
            $caseload = $this->getRecord();

            $this
                ->getFilteredTableQuery()
                ->chunk(1000, function ($subjects) use ($caseload) {
                    $subjects
                        ->each(function ($item) use ($caseload) {
                            $subject = new CaseloadSubject();
                            $subject->subject()->associate($item);
                            $subject->caseload()->associate($caseload);
                            $subject->save();
                        });
                });
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->data['type'] === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        }

        return $data;
    }
}
