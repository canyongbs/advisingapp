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

namespace Assist\Task\Imports;

use App\Models\User;
use App\Models\Import;
use App\Imports\Importer;
use AllowDynamicProperties;
use Illuminate\Support\Str;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Assist\Prospect\Models\Prospect;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use App\Filament\Actions\ImportAction\ImportColumn;

/**
 * @property ?Task $record
 */
#[AllowDynamicProperties]
class TaskImporter extends Importer
{
    /**
     * @inheritDoc
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->example('The task title'),
            ImportColumn::make('description')
                ->requiredMapping()
                ->example('A description of the task.'),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules([new Enum(TaskStatus::class)])
                ->example('pending'),
            ImportColumn::make('due')
                ->rules(['date'])
                ->example('1990-01-01 00:00:00'),
            ImportColumn::make('assignedTo')
                ->relationship(
                    resolveUsing: fn (mixed $state) => User::query()
                        ->when(
                            Str::isUuid($state),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->where('email', $state),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => auth()->user()?->email ?? User::query()->value('email')),
            ImportColumn::make('concern')
                ->label('Related To')
                ->relationship(
                    resolveUsing: function (mixed $state) {
                        $type = str($state)->before(':');
                        $value = str($state)->after(':');

                        return match ($type->toString()) {
                            'prospect' => Prospect::query()
                                ->when(
                                    str($value)->isUuid(),
                                    fn (Builder $query) => $query->whereKey($value),
                                    fn (Builder $query) => $query->where('email', $value),
                                )
                                ->first(),
                            'student' => Student::query()
                                ->when(
                                    str($value)->isUuid(),
                                    fn (Builder $query) => $query->whereKey($value),
                                    fn (Builder $query) => $query->where('email', $value),
                                )
                                ->first(),
                        };
                    },
                )
                ->requiredMapping()
                ->rules(
                    [
                        'starts_with:prospect:,student:',
                    ]
                )
                ->example('student:johnsmith@gmail.com'),
        ];
    }

    public function resolveRecord(): Task
    {
        return new Task();
    }

    public function afterFill(): void
    {
        /** @var Task $record */
        $record = $this->record;

        $query = Task::query();

        foreach ($record->getAttributes() as $key => $value) {
            if (in_array($key, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $query->where($key, $value);
        }

        $existingRecord = $query->first();

        if ($existingRecord) {
            $this->record = $existingRecord;
        }
    }

    public function beforeCreate(): void
    {
        $this->record->createdBy()->associate($this->import->user);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your tasks import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
