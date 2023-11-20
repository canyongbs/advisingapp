<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class GetCaseloadQuery extends EditRecord implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }

    protected static string $resource = CaseloadResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    public function afterFill(): void
    {
        $this->data['model'] = CaseloadModel::from($this->data['model']);
        $this->data['type'] = CaseloadType::from($this->data['type']);
        $this->data['user']['name'] = $this->getRecord()->user->name;
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
}
