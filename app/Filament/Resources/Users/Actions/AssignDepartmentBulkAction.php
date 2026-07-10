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

namespace App\Filament\Resources\Users\Actions;

use AdvisingApp\Team\Models\Department;
use App\Models\User;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Collection;

class AssignDepartmentBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-user-group')
            ->modalWidth(Width::Small)
            ->modalDescription(
                fn (Collection $records): string => 'This bulk action will overwrite any prior department assignments for the selected ' . ((count($records) > 1) ? 'users' : 'user') . '.'
            )
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
            ])
            ->form([
                Select::make('department')
                    ->label('Department')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, Collection $records) {
                $success = 0;
                $fail = 0;
                /** @var Collection <int, User> $records */
                $records->each(function (User $record) use ($data, &$success, &$fail) {
                    try {
                        $record->assignDepartment($data['department']);
                        $success++;
                    } catch (Exception $exception) {
                        report($exception);
                        $fail++;
                    }
                });

                if ($fail > 0) {
                    Notification::make()
                        ->title('Assigned Department')
                        ->body($fail . ' ' . (($fail > 1) ? 'users were' : 'user was') . ' failed to be added to the department.')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Assigned Department')
                        ->body($success . ' ' . (($success > 1) ? 'users were' : 'user was') . ' successfully added to the department.')
                        ->success()
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): ?string
    {
        return 'Assign department';
    }
}
