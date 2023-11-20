<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class EditCaseload extends EditRecord implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }

    protected static string $resource = CaseloadResource::class;

    protected static string $view = 'caseload-management::filament.resources.caseloads.pages.edit-caseload';

    public function afterFill(): void
    {
        $this->data['model'] = CaseloadModel::from($this->data['model']);
        $this->data['type'] = CaseloadType::from($this->data['type']);
        $this->data['user']['name'] = $this->getRecord()->user->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autocomplete(false)
                    ->string()
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Grid::make()
                    ->schema([
                        Select::make('type')
                            ->options(CaseloadType::class)
                            ->disabled(),
                        Select::make('model')
                            ->label('Population')
                            ->options(CaseloadModel::class)
                            ->disabled(),
                        TextInput::make('user.name')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(CaseloadResource::columns($this->data['model']))
            ->filters(CaseloadResource::filters($this->data['model']), layout: FiltersLayout::AboveContent)
            // ->actions(CaseloadResource::actions($this->data['model']))
            ->query(function () {
                $model = $this->data['model'];
                $query = $model->query();

                if ($this->getRecord()->type === CaseloadType::Static) {
                    $column = app($model->class())->getKeyName();
                    $ids = $this->getRecord()->subjects()->pluck('subject_id');

                    $query->whereIn($column, $ids);
                }

                return $query;
            });
    }

    public function bootedInteractsWithTable(): void
    {
        if ($this->shouldMountInteractsWithTable) {
            $this->tableFilters = $this->getRecord()->filters;
        }

        $this->baseBootedInteractsWithTable();
    }

    public function afterSave(): void {}

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (CaseloadType::tryFromCaseOrValue($this->data['type']) === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } else {
            $data['filters'] = [];
        }

        return $data;
    }
}
