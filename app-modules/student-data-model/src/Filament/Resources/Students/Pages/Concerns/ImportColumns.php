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
                ->label('Academic Career')
                ->exampleHeader('Academic Career')
                ->example('CRED')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('division')
                ->label('College')
                ->exampleHeader('College')
                ->example('ABC01')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('acad_plan_majors')
                ->label('ACAD Plan Majors')
                ->exampleHeader('ACAD Plan Majors')
                ->example('Computer Science|Mathematics')
                ->array('|')
                ->rules(['array'])
                ->nestedRecursiveRules(['string', 'max:255'])
                ->fillRecordUsing(function (Program $record, array $state) {
                    $acadPlan = $record->acad_plan ?? [];

                    if (! is_array($record->acad_plan)) {
                        $acadPlan = [];
                    }

                    $acadPlan['major'] = $state;

                    $record->acad_plan = $acadPlan;
                }),
            ImportColumn::make('acad_plan_minors')
                ->label('ACAD Plan Minors')
                ->exampleHeader('ACAD Plan Minors')
                ->example('Philosophy|Psychology')
                ->array('|')
                ->rules(['array'])
                ->nestedRecursiveRules(['string', 'max:255'])
                ->fillRecordUsing(function (Program $record, array $state) {
                    $acadPlan = $record->acad_plan ?? [];

                    if (! is_array($record->acad_plan)) {
                        $acadPlan = [];
                    }

                    $acadPlan['minor'] = $state;

                    $record->acad_plan = $acadPlan;
                }),
            ImportColumn::make('prog_status')
                ->label('Program Status')
                ->exampleHeader('Program Status')
                ->example('AC')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('cum_gpa')
                ->label('Cumulative GPA')
                ->exampleHeader('Cumulative GPA')
                ->numeric()
                ->example('3.284')
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('semester')
                ->label('Semester')
                ->exampleHeader('Semester')
                ->example('1234')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('descr')
                ->label('Name')
                ->exampleHeader('Name')
                ->example('Bachelor of Science')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('foi')
                ->label('Field of Interest')
                ->exampleHeader('Field of Interest')
                ->example('Computer Science')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('change_dt')
                ->label('Change Date')
                ->exampleHeader('Change Date')
                ->example('1986-06-13 08:11:35')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('declare_dt')
                ->label('Start Date')
                ->exampleHeader('Start Date')
                ->example('1986-06-13 08:11:35')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('graduation_dt')
                ->label('Graduation Date')
                ->exampleHeader('Graduation Date')
                ->example('1986-06-13 08:11:35')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('catalog_year')
                ->label('Catalog Year')
                ->exampleHeader('Catalog Year')
                ->example('2024')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
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
