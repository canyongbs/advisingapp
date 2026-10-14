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

use App\Features\StudentGroupFilterConsolidationFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Legacy name constraint types, keyed to their attribute in the consolidated `name` constraint.
     *
     * @var array<string, string>
     */
    private const NAME_ATTRIBUTES = [
        'full_name' => 'fullName',
        'first' => 'firstName',
        'last' => 'lastName',
        'preferred' => 'preferredName',
    ];

    /**
     * @var array<int, string>
     */
    private const REMOVED_TYPES = [
        'sisid',
        'otherid',
        'email',
        'phone',
        'address',
        'address_2',
        'enrollmentSisid',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $groupIdsToDelete = [];

            $this->eachStudentGroupWithRules(function (object $group, array $filters) use (&$groupIdsToDelete): void {
                $rules = $filters['queryBuilder']['rules'];

                if ($this->containsRule($rules, fn (array $rule): bool => in_array($rule['type'] ?? null, self::REMOVED_TYPES, true))) {
                    if ($group->deleted_at === null) {
                        $groupIdsToDelete[] = $group->id;
                    }

                    return;
                }

                $filters['queryBuilder']['rules'] = $this->mapRules($rules, function (array $rule): array {
                    $attribute = self::NAME_ATTRIBUTES[$rule['type'] ?? ''] ?? null;

                    if ($attribute === null) {
                        return $rule;
                    }

                    return [
                        'type' => 'name',
                        'data' => [
                            'operator' => $attribute,
                            'settings' => [
                                'operator' => $rule['data']['operator'] ?? null,
                                'text' => $rule['data']['settings']['text'] ?? null,
                            ],
                        ],
                    ];
                });

                $this->updateFiltersIfChanged($group, $filters);
            });

            // Campaigns require a group, so they are deleted with it, matching how both are soft deleted in the app.
            foreach (array_chunk($groupIdsToDelete, 1000) as $groupIds) {
                DB::table('campaigns')
                    ->whereIn('segment_id', $groupIds)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now(), 'updated_at' => now()]);

                DB::table('segments')
                    ->whereIn('id', $groupIds)
                    ->update(['deleted_at' => now(), 'updated_at' => now()]);
            }

            StudentGroupFilterConsolidationFeature::activate();
        });
    }

    /**
     * Deleted groups and campaigns are not restored, as they cannot be told apart from ones deleted by users.
     */
    public function down(): void
    {
        DB::transaction(function () {
            StudentGroupFilterConsolidationFeature::deactivate();

            $legacyTypes = array_flip(self::NAME_ATTRIBUTES);

            $this->eachStudentGroupWithRules(function (object $group, array $filters) use ($legacyTypes): void {
                $filters['queryBuilder']['rules'] = $this->mapRules($filters['queryBuilder']['rules'], function (array $rule) use ($legacyTypes): array {
                    $legacyType = $legacyTypes[$rule['data']['operator'] ?? ''] ?? null;

                    if ((($rule['type'] ?? null) !== 'name') || ($legacyType === null)) {
                        return $rule;
                    }

                    return [
                        'type' => $legacyType,
                        'data' => [
                            'operator' => $rule['data']['settings']['operator'] ?? null,
                            'settings' => [
                                'text' => $rule['data']['settings']['text'] ?? null,
                            ],
                        ],
                    ];
                });

                $this->updateFiltersIfChanged($group, $filters);
            });
        });
    }

    /**
     * @param Closure(object, array<string, mixed>): void $callback
     */
    private function eachStudentGroupWithRules(Closure $callback): void
    {
        DB::table('segments')
            ->where('model', 'student')
            ->where('type', 'dynamic')
            ->whereNotNull('filters')
            ->chunkById(100, function (Collection $groups) use ($callback): void {
                foreach ($groups as $group) {
                    $filters = json_decode($group->filters, associative: true);

                    if (! is_array($filters['queryBuilder']['rules'] ?? null)) {
                        continue;
                    }

                    $callback($group, $filters);
                }
            });
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function updateFiltersIfChanged(object $group, array $filters): void
    {
        if ($filters === json_decode($group->filters, associative: true)) {
            return;
        }

        DB::table('segments')
            ->where('id', $group->id)
            ->update(['filters' => json_encode($filters)]);
    }

    /**
     * Maps every rule, including rules nested inside "or" blocks, preserving rule keys.
     *
     * @param array<array-key, mixed> $rules
     * @param Closure(array<string, mixed>): array<string, mixed> $map
     *
     * @return array<array-key, mixed>
     */
    private function mapRules(array $rules, Closure $map): array
    {
        foreach ($rules as $ruleKey => $rule) {
            if (! is_array($rule)) {
                continue;
            }

            if (($rule['type'] ?? null) !== 'or') {
                $rules[$ruleKey] = $map($rule);

                continue;
            }

            foreach ($rule['data']['groups'] ?? [] as $orGroupKey => $orGroup) {
                if (is_array($orGroup['rules'] ?? null)) {
                    $rules[$ruleKey]['data']['groups'][$orGroupKey]['rules'] = $this->mapRules($orGroup['rules'], $map);
                }
            }
        }

        return $rules;
    }

    /**
     * @param array<array-key, mixed> $rules
     * @param Closure(array<string, mixed>): bool $test
     */
    private function containsRule(array $rules, Closure $test): bool
    {
        $contains = false;

        $this->mapRules($rules, function (array $rule) use ($test, &$contains): array {
            $contains = $contains || $test($rule);

            return $rule;
        });

        return $contains;
    }
};
