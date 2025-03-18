<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Task\Imports;

use AdvisingApp\Interaction\Imports\InteractionsImporter;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use AllowDynamicProperties;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

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
                    resolveUsing: function (InteractionsImporter $importer, mixed $state): ?Model {
                        $resolveFromModel = fn (string $model, string $identifier): ?Model => $model::query()
                            ->when(
                                str($identifier)->isUuid(),
                                fn (Builder $query) => $query->whereKey($identifier),
                                fn (Builder $query) => $query->whereRelation('primaryEmail', 'address', $identifier),
                            )
                            ->first();

                        if (str($state)->contains(':')) {
                            return $resolveFromModel(match ((string) str($state)->before(':')) {
                                'prospect' => Prospect::class,
                                'student' => Student::class,
                            }, (string) str($state)->after(':'));
                        }

                        $user = $importer->getImport()->user;

                        $model = match (true) {
                            $user->hasLicense(Student::getLicenseType()) => Student::class,
                            $user->hasLicense(Prospect::getLicenseType()) => Prospect::class,
                            default => null,
                        };

                        return filled($model) ? $resolveFromModel($model, $state) : null;
                    },
                )
                ->requiredMapping()
                ->rules(function (InteractionsImporter $importer) {
                    if (! $importer->getImport()->user->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
                        return [];
                    }

                    return ['starts_with:prospect:,student:'];
                })
                ->example(function () {
                    if (auth()->user()?->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()]) ?? true) {
                        return 'student:johnsmith@gmail.com';
                    }

                    return 'johnsmith@gmail.com';
                }),
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

        if ($this->import->user->cannot('view', $record->concern)) {
            throw ValidationException::withMessages([
                'concern' => 'You do not have permission to create a task for this concern.',
            ]);
        }

        if ($record->assignedTo?->cannot('view', $record->concern)) {
            throw ValidationException::withMessages([
                'assignedTo' => 'The assigned user does not have permission to view this concern.',
            ]);
        }

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
        /** @var Task $record */
        $record = $this->record;

        /** @var User $user */
        $user = $this->import->user;

        $record->createdBy()->associate($user);
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
