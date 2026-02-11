<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\Concerns;

use AdvisingApp\StudentDataModel\Models\Program;
use Filament\Actions\Imports\ImportColumn;

trait ImportColumns
{
    /**
     * @return array<ImportColumn>
     */
    public static function getProgramColumns(): array
    {
        return [
            ImportColumn::make('acad_career')
                ->label('ACAD career')
                ->example('CRED')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('division')
                ->example('ABC01')
                ->example('ABC01')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('acad_plan_majors')
                ->label('ACAD plan majors')
                ->example('1076N|1077N')
                ->array('|')
                ->rules(['array'])
                ->nestedRecursiveRules(['string', 'max:255'])
                ->fillRecordUsing(function (Program $record, array $state) {
                    $acadPlan = $record->acad_plan ?? [];

                    if (! is_array($record->acad_plan)) {
                        $acadPlan = [];
                    }

                    $acadPlan['majors'] = $state;

                    $record->acad_plan = $acadPlan;
                }),
            ImportColumn::make('acad_plan_minors')
                ->label('ACAD plan minors')
                ->example('2076N|2077N')
                ->array('|')
                ->rules(['array'])
                ->nestedRecursiveRules(['string', 'max:255'])
                ->fillRecordUsing(function (Program $record, array $state) {
                    $acadPlan = $record->acad_plan ?? [];

                    if (! is_array($record->acad_plan)) {
                        $acadPlan = [];
                    }

                    $acadPlan['minors'] = $state;

                    $record->acad_plan = $acadPlan;
                }),
            ImportColumn::make('prog_status')
                ->label('PROG status')
                ->example('AC')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('cum_gpa')
                ->label('Cum GPA')
                ->numeric()
                ->example('3.284')
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('semester')
                ->example('1234')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('descr')
                ->label('DESCR')
                ->example('Loream ipsum')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('foi')
                ->label('Field of interest')
                ->example('Loream ipsum')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('change_dt')
                ->label('Change date')
                ->example('1986-06-13 08:11:35')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('declare_dt')
                ->label('Declare date')
                ->example('1986-06-13 08:11:35')
                ->rules([
                    'nullable',
                    'date',
                ]),
        ];
    }

    /**
     * @return array<ImportColumn>
     */
    public static function getEnrollmentColumns(): array
    {
        return [
            ImportColumn::make('division')
                ->example('ABC01')
                ->label('Division')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('class_nbr')
                ->label('Class NBR')
                ->example('19309')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('crse_grade_off')
                ->example('A')
                ->label('CRSE grade off')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('unt_taken')
                ->label('UNT taken')
                ->example('1')
                ->numeric()
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('unt_earned')
                ->label('UNT earned')
                ->example('1')
                ->numeric()
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('last_upd_dt_stmp')
                ->label('Last UPD date STMP')
                ->example('1995-02-11 14:01:12')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('section')
                ->label('Section')
                ->example('7661')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('name')
                ->label('Name')
                ->example('Introduction to Mathematics')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('department')
                ->label('Department')
                ->example('Business')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('faculty_name')
                ->label('Faculty name')
                ->example('Keyon Metz')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('faculty_email')
                ->label('Faculty email')
                ->example('jerry72@example.net')
                ->rules([
                    'nullable',
                    'email',
                    'max:255',
                ]),
            ImportColumn::make('semester_code')
                ->label('Semester code')
                ->example('4209')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('semester_name')
                ->label('Semester name')
                ->example('Fall 2006')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('start_date')
                ->label('Start date')
                ->example('2001-09-30 19:55:54')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('end_date')
                ->label('End date')
                ->example('2001-09-30 19:55:54')
                ->rules([
                    'nullable',
                    'date',
                ]),
        ];
    }
}
