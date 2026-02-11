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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use Illuminate\Support\Facades\DB;

class RemoveAlertFiltersFromGroups
{
    /**
     * @param array<string> $alertConfigurationIds
     */
    public function execute(array $alertConfigurationIds): int
    {
        return DB::transaction(function () use ($alertConfigurationIds): int {
            $modifiedCount = 0;

            $groups = Group::query()
                ->where('model', GroupModel::Student)
                ->where('type', GroupType::Dynamic)
                ->whereNotNull('filters')
                ->get();

            foreach ($groups as $group) {
                $filters = $group->filters;

                if (empty($filters['queryBuilder']['rules'])) {
                    continue;
                }

                $originalRules = $filters['queryBuilder']['rules'];
                $cleanedRules = $this->removeAlertRulesFromRules(
                    $originalRules,
                    $alertConfigurationIds
                );

                if ($cleanedRules !== $originalRules) {
                    $filters['queryBuilder']['rules'] = $cleanedRules;
                    $group->filters = $filters;
                    $group->save();
                    $modifiedCount++;
                }
            }

            return $modifiedCount;
        });
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     * @param array<string> $alertConfigurationIds
     *
     * @return array<int, array<string, mixed>>
     */
    protected function removeAlertRulesFromRules(array $rules, array $alertConfigurationIds): array
    {
        $cleaned = [];

        foreach ($rules as $key => $rule) {
            if (isset($rule['type']) && $rule['type'] === 'or') {
                if (isset($rule['data']['groups']) && is_array($rule['data']['groups'])) {
                    $cleanedGroups = [];

                    foreach ($rule['data']['groups'] as $group) {
                        if (isset($group['rules'])) {
                            $cleanedGroupRules = $this->removeAlertRulesFromRules(
                                $group['rules'],
                                $alertConfigurationIds
                            );

                            if (! empty($cleanedGroupRules)) {
                                $group['rules'] = $cleanedGroupRules;
                                $cleanedGroups[] = $group;
                            }
                        }
                    }

                    if (! empty($cleanedGroups)) {
                        $rule['data']['groups'] = $cleanedGroups;
                        $cleaned[$key] = $rule;
                    }
                }

                continue;
            }

            if (
                isset($rule['type']) && $rule['type'] === 'alertStatus' &&
                isset($rule['data']['settings']['alert_configuration_id']) &&
                in_array($rule['data']['settings']['alert_configuration_id'], $alertConfigurationIds, true)
            ) {
                continue;
            }

            $cleaned[$key] = $rule;
        }

        return $cleaned;
    }
}
