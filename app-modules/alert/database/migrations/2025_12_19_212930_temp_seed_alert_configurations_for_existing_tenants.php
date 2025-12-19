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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Alert\Configurations\AdultLearnerAlertConfiguration;
use AdvisingApp\Alert\Configurations\CumulativeGpaAlertConfiguration;
use AdvisingApp\Alert\Configurations\NewStudentAlertConfiguration;
use AdvisingApp\Alert\Configurations\SemesterGpaAlertConfiguration;
use App\Features\AlertConfigurationFeature;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        $presets = [
            [
                'preset' => 'd_or_f_grade',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'multiple_d_or_f_grades',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'course_withdrawal',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'multiple_course_withdrawals',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'repeated_course_attempt',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'cumulative_gpa_below_threshold',
                'config_model' => CumulativeGpaAlertConfiguration::class,
                'config_data' => ['gpa_threshold' => 2.00],
            ],
            [
                'preset' => 'semester_gpa_below_threshold',
                'config_model' => SemesterGpaAlertConfiguration::class,
                'config_data' => ['gpa_threshold' => 2.00],
            ],
            [
                'preset' => 'first_generation_student',
                'config_model' => null,
                'config_data' => null,
            ],
            [
                'preset' => 'adult_learner',
                'config_model' => AdultLearnerAlertConfiguration::class,
                'config_data' => ['minimum_age' => 24],
            ],
            [
                'preset' => 'new_student',
                'config_model' => NewStudentAlertConfiguration::class,
                'config_data' => ['number_of_semesters' => 1],
            ],
        ];

        DB::transaction(function () use ($presets) {
            foreach ($presets as $presetData) {
                $exists = DB::table('alert_configurations')
                    ->where('preset', $presetData['preset'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $configurationId = null;
                $configurationType = null;

                if ($presetData['config_model']) {
                    $configId = (string) Str::uuid();
                    $now = now();

                    $modelClass = $presetData['config_model'];
                    $tableName = (new $modelClass())->getTable();

                    DB::table($tableName)->insert(
                        array_merge(
                            ['id' => $configId],
                            $presetData['config_data'],
                            [
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        )
                    );

                    $configurationId = $configId;
                    $configurationType = array_search($modelClass, Relation::morphMap(), true) ?: $modelClass;
                }

                DB::table('alert_configurations')->insert([
                    'id' => (string) Str::uuid(),
                    'preset' => $presetData['preset'],
                    'is_enabled' => false,
                    'configuration_id' => $configurationId,
                    'configuration_type' => $configurationType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            AlertConfigurationFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $alertConfigurations = DB::table('alert_configurations')->get();

            foreach ($alertConfigurations as $alertConfig) {
                if ($alertConfig->configuration_id && $alertConfig->configuration_type) {
                    $tableName = $this->getTableNameFromType($alertConfig->configuration_type);

                    if ($tableName) {
                        DB::table($tableName)
                            ->where('id', $alertConfig->configuration_id)
                            ->delete();
                    }
                }
            }

            DB::table('alert_configurations')->truncate();

            AlertConfigurationFeature::deactivate();
        });
    }

    private function getTableNameFromType(string $type): ?string
    {
        if (! class_exists($type)) {
            return null;
        }

        return (new $type())->getTable();
    }
};
