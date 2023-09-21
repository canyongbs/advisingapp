<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Models\CaseloadSubject;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class EditCaseload extends EditRecord implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }

    protected static string $resource = CaseloadResource::class;

    protected static string $view = 'filament.resources.caseloads.pages.edit-caseload';

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
            ->filters(CaseloadResource::filters($this->data['model']))
            // ->actions(CaseloadResource::actions($this->data['model']))
            ->query(function () {
                $model = $this->data['model'];
                $query = $model->query();

                if ($this->data['type'] === CaseloadType::Static) {
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

    public function afterSave(): void
    {
        if ($this->data['type'] === CaseloadType::Static) {
            $caseload = $this->getRecord();
            $query = $this->getFilteredTableQuery();

            $caseload
                ->subjects()
                ->chunk(1000, fn ($subjects) => $subjects->each->delete());

            $query
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->data['type'] === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        }

        return $data;
    }
}
