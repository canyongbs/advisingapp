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

namespace AdvisingApp\Alert\Actions;

use AdvisingApp\Alert\Contracts\AlertPresetConfiguration;
use AdvisingApp\Alert\Models\AlertConfiguration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class GenerateStudentAlertsView
{
    public function execute(): void
    {
        DB::transaction(function () {
            $alertConfigurations = AlertConfiguration::with('configuration')
                ->where('is_enabled', true)
                ->get();

            if ($alertConfigurations->isEmpty()) {
                DB::statement('DROP VIEW IF EXISTS student_alerts');
                DB::statement(<<<'SQL'
                    CREATE VIEW student_alerts AS
                    SELECT
                        NULL::character varying(255) AS sisid,
                        NULL::uuid AS alert_configuration_id
                    WHERE false
                SQL);

                return;
            }

            $unionQueries = [];

            foreach ($alertConfigurations as $config) {
                $handler = $config->preset->getHandler();

                /** @var AlertPresetConfiguration|null $configuration */
                $configuration = $config->configuration;
                $studentQuery = $handler->getStudentAlertQuery($configuration);

                $alertId = $config->id;

                if (! Str::isUuid($alertId)) {
                    throw new InvalidArgumentException("Invalid UUID format for alert configuration ID: {$alertId}");
                }

                $safeAlias = 'subquery_' . str_replace('-', '_', $config->id);

                $subquerySql = $this->getInlinedSql($studentQuery);
                $unionQueries[] = "SELECT sisid, '{$alertId}'::uuid AS alert_configuration_id FROM ({$subquerySql}) AS {$safeAlias}";
            }

            DB::statement('DROP VIEW IF EXISTS student_alerts');
            $viewSql = 'CREATE VIEW student_alerts AS ' . implode(' UNION ALL ', $unionQueries);

            DB::statement($viewSql);
        });
    }

    /**
     * @param Builder $query
     *
     * @return string
     */
    private function getInlinedSql(Builder $query): string
    {
        return $query->toRawSql();
    }
}
