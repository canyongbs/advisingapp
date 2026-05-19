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

namespace AdvisingApp\StudentDataModel\Filament\Resources\EnrollmentSemesters\Pages;

use AdvisingApp\StudentDataModel\Filament\Resources\EnrollmentSemesters\EnrollmentSemesterResource;
use AdvisingApp\StudentDataModel\Filament\Tables\UnsyncedEnrollmentsTable;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TableSelect;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManageEnrollmentSemesters extends ManageRecords
{
    protected static string $resource = EnrollmentSemesterResource::class;

    protected static ?string $title = 'Semester Order';

    protected function getHeaderActions(): array
    {
        $unsyncedEnrollments = UnsyncedEnrollmentsTable::getUnsyncedEnrollments();

        return [
            Action::make('syncAll')
                ->label('Sync')
                ->disabled(fn() => $unsyncedEnrollments->count() === 0)
                ->tooltip(fn () => $unsyncedEnrollments->count() === 0 ? 'No additional semesters to sync.' : '')
                ->slideOver()
                ->schema([
                    TableSelect::make('enrollments')
                        ->hiddenLabel()
                        ->multiple()
                        ->required()
                        ->default(fn () => $unsyncedEnrollments->pluck('id'))
                        ->tableConfiguration(UnsyncedEnrollmentsTable::class),
                ])
                ->action(function (array $data) {
                    try {
                        DB::beginTransaction();

                        $enrollments = Enrollment::whereIn('id', $data['enrollments'])->get();

                        $enrollments->lazy()->each(fn (Enrollment $enrollment) => is_null($enrollment->semester_name) ?: EnrollmentSemester::create(['name' => $enrollment->semester_name]));

                        DB::commit();

                        Notification::make()
                            ->success()
                            ->title('Sync Successful')
                            ->send();
                    } catch (Throwable $throw) {
                        DB::rollBack();

                        Notification::make()
                            ->danger()
                            ->title('Sync Failed')
                            ->body('Please try again. If this issue persists, reach out to support.')
                            ->send();

                        report($throw);
                    }
                }),
            CreateAction::make()
                ->label('New')
                ->modalHeading('Add Semester Manually')
                ->modalDescription('Use this feature to add a semester to your semester list manually.')
                ->modalSubmitActionLabel('Add')
                ->createAnotherAction(fn (Action $action) => $action->label('Add & Add Another')),
        ];
    }
}
