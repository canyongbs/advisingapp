<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
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

    protected static string $view = 'filament.resources.caseloads.pages.edit-caseload';

    public function afterFill(): void
    {
        $this->data['model'] = CaseloadModel::from($this->data['model']);
        $this->data['type'] = CaseloadType::from($this->data['type']);
    }

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
                    ->disabled(),
                Select::make('model')
                    ->label('Population')
                    ->options(CaseloadModel::class)
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(CaseloadResource::columns($this->data['model']))
            ->filters(CaseloadResource::filters($this->data['model']))
            ->query(fn () => $this->data['model']->query());
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
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->data['type'] === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } elseif ($this->data['type'] === CaseloadType::Static) {
            // ray($this->getFilteredTableQuery()->get());
            //bulk insert
        }

        return $data;
    }
}
