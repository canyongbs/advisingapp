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

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sisid')
                ->label('Student ID')
                ->requiredMapping()
                ->example('########')
                ->rules([
                    'required',
                    'string',
                    'alpha_dash',
                    'max:255',
                ]),
            ImportColumn::make('otherid')
                ->label('Other ID')
                ->example('##########')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('first')
                ->example('Jonathan')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('last')
                ->example('Smith')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('full_name')
                ->example('Jonathan Smith')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('preferred')
                ->example('John')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('birthdate')
                ->example('2024-10-21')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('hsgrad')
                ->example(fn () => '2025-11-21')
                ->rules(fn () => [
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('gender')
                ->example('Male')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('dual')
                ->example('true')
                ->boolean()
                ->rules([
                    'nullable',
                    'boolean',
                ]),
            ImportColumn::make('ferpa')
                ->label('FERPA')
                ->example('true')
                ->boolean()
                ->rules([
                    'nullable',
                    'boolean',
                ]),
            ImportColumn::make('dfw')
                ->label('DFW')
                ->example('2024-10-21')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('sap')
                ->label('SAP')
                ->example('true')
                ->boolean()
                ->rules([
                    'nullable',
                    'boolean',
                ]),
            ImportColumn::make('holds')
                ->example('UHIJN')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('standing')
                ->label('Academic Standing')
                ->example('Suspended')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('firstgen')
                ->example('true')
                ->boolean()
                ->rules([
                    'nullable',
                    'boolean',
                ]),
            ImportColumn::make('ethnicity')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('lastlmslogin')
                ->label('Last LMS login')
                ->example('2024-10-21 12:00:00')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('athletics_status')
                ->label('Athletics Status')
                ->example('Active')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('athletic_details')
                ->label('Athletic Details')
                ->example('Football, 2024')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('care_team_1')
                ->label('Care Team 1')
                ->example('joesmith@gmail.com')
                ->rules([
                    'nullable',
                    'email',
                    'max:255',
                    Rule::exists(User::class, 'email'),
                    'different:care_team_2',
                    'different:care_team_3',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('care_team_role_1')
                ->label('Care Team Role 1')
                ->example('Advising')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('care_team_2')
                ->label('Care Team 2')
                ->example('janesmith@gmail.com')
                ->rules([
                    'nullable',
                    'email',
                    'max:255',
                    Rule::exists(User::class, 'email'),
                    'different:care_team_1',
                    'different:care_team_3',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('care_team_role_2')
                ->label('Care Team Role 2')
                ->example('Aid')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('care_team_3')
                ->label('Care Team 3')
                ->rules([
                    'nullable',
                    'email',
                    'max:255',
                    Rule::exists(User::class, 'email'),
                    'different:care_team_1',
                    'different:care_team_2',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('care_team_role_3')
                ->label('Care Team Role 3')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('created_at_source')
                ->label('Create date/time')
                ->example('2024-10-21 12:00:00')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('updated_at_source')
                ->label('Update date/time')
                ->example('2024-10-21 12:00:00')
                ->rules([
                    'nullable',
                    'date',
                ]),
        ];
    }

    public function resolveRecord(): Student
    {
        return (new Student())->setTable("import_{$this->import->getKey()}_students");
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function getJobBatchName(): ?string
    {
        return "student-import-{$this->getImport()->getKey()}";
    }

    protected function afterCreate(): void
    {
        $student = $this->record;

        assert($student instanceof Student);

        foreach ([
            ['care_team_1', 'care_team_role_1'],
            ['care_team_2', 'care_team_role_2'],
            ['care_team_3', 'care_team_role_3'],
        ] as [$careTeamColumn, $careTeamRoleColumn]) {
            if (blank($this->data[$careTeamColumn] ?? null)) {
                continue;
            }

            $user = User::query()
                ->where('email', $this->data[$careTeamColumn])
                ->first();

            if (! $user) {
                continue;
            }

            if ($student->careTeam()->whereKey($user)->exists()) {
                continue;
            }

            if (filled($this->data[$careTeamRoleColumn])) {
                $careTeamRole = CareTeamRole::query()
                    ->where(new Expression('lower(name)'), Str::lower($this->data[$careTeamRoleColumn]))
                    ->first();
            }

            if (! ($careTeamRole ?? null)) {
                $defaultCareTeamRole ??= CareTeamRoleType::studentDefault();
                $defaultCareTeamRole ??= CareTeamRole::query()->where('type', CareTeamRoleType::Student)->latest()->first();

                $careTeamRole = $defaultCareTeamRole;
            }

            $careTeam = new CareTeam();
            $careTeam->setTable("import_{$this->import->getKey()}_care_teams");
            $careTeam->user()->associate($user);
            $careTeam->educatable()->associate($student);
            $careTeam->careTeamRole()->associate($careTeamRole);
            $careTeam->save();
        }
    }
}
