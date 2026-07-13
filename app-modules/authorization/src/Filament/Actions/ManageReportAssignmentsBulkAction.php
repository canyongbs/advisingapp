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

namespace AdvisingApp\Authorization\Filament\Actions;

use AdvisingApp\Report\Models\ReportDepartmentAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\Team\Models\Department;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManageReportAssignmentsBulkAction
{
    public static function make(): BulkAction
    {
        $usersQuery = User::query()
            ->tap(new WithoutAnyAdmin())
            ->orderBy('name');

        $departmentsQuery = Department::query()
            ->orderBy('name');

        return BulkAction::make('manageReportAssignments')
            ->icon('heroicon-s-user-group')
            ->label('Manage Assignments')
            ->modalHeading(fn (Collection $records): string => 'Manage Assignments for ' . $records->count() . ' ' . str('Report')->plural($records->count()))
            ->modalDescription('Grant access to the selected reports by assigning individual users and/or departments.')
            ->form([
                Select::make('users')
                    ->label('Users')
                    ->multiple()
                    ->searchable()
                    ->options(
                        fn (): array => $usersQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $usersQuery
                            ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelsUsing(
                        fn (array $values): array => $usersQuery
                            ->whereKey($values)
                            ->pluck('name', 'id')
                            ->all(),
                    ),
                Select::make('departments')
                    ->label('Departments')
                    ->multiple()
                    ->searchable()
                    ->options(
                        fn (): array => $departmentsQuery
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getSearchResultsUsing(
                        fn (string $search): array => $departmentsQuery
                            ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->getOptionLabelsUsing(
                        fn (array $values): array => $departmentsQuery
                            ->whereKey($values)
                            ->pluck('name', 'id')
                            ->all(),
                    ),
                Toggle::make('sync')
                    ->label('Replace existing assignments?')
                    ->default(false)
                    ->hintIconTooltip('If checked, all existing user and department assignments will be removed and replaced with the selections above.'),
            ])
            ->action(function (array $data, Collection $records): void {
                $userIds = $data['users'] ?? [];
                $departmentIds = $data['departments'] ?? [];
                $sync = $data['sync'];

                try {
                    DB::transaction(function () use ($records, $userIds, $departmentIds, $sync): void {
                        $records->each(function (array $record) use ($userIds, $departmentIds, $sync): void {
                            $reportKey = $record['report_key'];

                            if ($sync) {
                                ReportUserAccess::query()
                                    ->where('report_key', $reportKey)
                                    ->whereNotIn('user_id', $userIds)
                                    ->delete();

                                ReportDepartmentAccess::query()
                                    ->where('report_key', $reportKey)
                                    ->whereNotIn('team_id', $departmentIds)
                                    ->delete();
                            }

                            $existingUserIds = ReportUserAccess::query()
                                ->where('report_key', $reportKey)
                                ->pluck('user_id')
                                ->all();

                            foreach (array_diff($userIds, $existingUserIds) as $userId) {
                                ReportUserAccess::query()->create([
                                    'report_key' => $reportKey,
                                    'user_id' => $userId,
                                ]);
                            }

                            $existingDepartmentIds = ReportDepartmentAccess::query()
                                ->where('report_key', $reportKey)
                                ->pluck('team_id')
                                ->all();

                            foreach (array_diff($departmentIds, $existingDepartmentIds) as $departmentId) {
                                ReportDepartmentAccess::query()->create([
                                    'report_key' => $reportKey,
                                    'team_id' => $departmentId,
                                ]);
                            }
                        });
                    });

                    Notification::make()
                        ->success()
                        ->title('Report assignments updated')
                        ->send();
                } catch (Throwable $exception) {
                    report($exception);

                    Notification::make()
                        ->danger()
                        ->title('Failed to update report assignments')
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }
}
