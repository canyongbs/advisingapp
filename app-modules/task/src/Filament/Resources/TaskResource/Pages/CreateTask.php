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

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Forms\Form;
use Filament\Facades\Filament;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Task\Filament\Resources\TaskResource;
use Filament\Forms\Components\MorphToSelect\Type;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                Textarea::make('description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->nullable()
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
                MorphToSelect::make('concern')
                    ->label('Related To')
                    ->searchable()
                    ->types([
                        Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data = collect($data);

        $record = new ($this->getModel())($data->except('assigned_to')->toArray());

        $record->assigned_to = $data->get('assigned_to');

        if ($tenant = Filament::getTenant()) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }
}
