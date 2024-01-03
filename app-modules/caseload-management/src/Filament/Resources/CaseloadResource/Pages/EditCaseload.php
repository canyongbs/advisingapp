<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

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
use AdvisingApp\Authorization\Enums\LicenseType;
use Filament\Tables\Concerns\InteractsWithTable;
use AdvisingApp\CaseloadManagement\Enums\CaseloadType;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;
use AdvisingApp\CaseloadManagement\Filament\Resources\CaseloadResource;

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
                            ->disabled()
                            ->visible(auth()->user()->hasLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])),
                        TextInput::make('user.name')
                            ->label('User')
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
