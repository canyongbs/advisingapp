<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Task\Filament\Schemas\Components;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Filament\Tables\AssignableTaskUsersTable;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Closure;
use Filament\Forms\Components\ModalTableSelect;
use Illuminate\Database\Eloquent\Builder;

class TaskAssignedToSelect
{
    public static function make(Student | Prospect $owner): ModalTableSelect
    {
        return ModalTableSelect::make('assigned_to')
            ->label('Assigned To')
            ->relationship(
                'assignedTo',
                'name',
                fn (Builder $query, ?Task $record): Builder => AssignableTaskUsersTable::scope(
                    $query,
                    $owner->getLicenseType(),
                    AssignableTaskUsersTable::alwaysIncludedUserIds($record),
                ),
            )
            ->tableConfiguration(AssignableTaskUsersTable::class)
            ->tableArguments(fn (?Task $record): array => [
                'licenseType' => $owner->getLicenseType()->value,
                'alwaysIncludedUserIds' => AssignableTaskUsersTable::alwaysIncludedUserIds($record),
            ])
            ->rules([
                fn (?Task $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($owner, $record): void {
                    if (blank($value)) {
                        return;
                    }

                    $isAssignable = AssignableTaskUsersTable::scope(
                        User::query()->whereKey($value),
                        $owner->getLicenseType(),
                        AssignableTaskUsersTable::alwaysIncludedUserIds($record),
                    )->exists();

                    if (! $isAssignable) {
                        $fail('The selected user cannot be assigned this task.');
                    }
                },
            ])
            ->nullable()
            ->default(auth()->id());
    }
}
