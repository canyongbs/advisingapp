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

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\JoinMatchedTerm;
use App\Features\MatchedEnrolledTermsFeature;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Converts First / Most Recent Enrolled Term rules saved before terms were matched, in filters that a data migration cannot reach
 * (e.g. a report's live filter kept in its URL), the same way the migration converted saved groups.
 *
 * The rules are renamed, and each semester name is replaced with the lowercased names of the terms that enrollments with it are
 * matched to, keeping a semester name no enrollment is matched by.
 */
class ConvertLegacyEnrolledTermRules
{
    /**
     * The enrolled term constraint types that compared enrollment semester names, keyed to the types that compare matched term names.
     *
     * @var array<string, string>
     */
    private const TYPES = [
        'firstEnrollmentTerm.semester_name' => 'firstEnrolledTerm',
        'mostRecentEnrollmentTerm.semester_name' => 'mostRecentEnrolledTerm',
    ];

    /**
     * @param array<array-key, mixed> $rules
     *
     * @return array<array-key, mixed>
     */
    public function __invoke(array $rules): array
    {
        // The legacy constraints are still registered while the feature is inactive, so their rules are left as they are.
        if (! MatchedEnrolledTermsFeature::active()) {
            return $rules;
        }

        $semesterNames = [];

        $this->mapRules($rules, function (array $rule) use (&$semesterNames): array {
            foreach ($this->getLegacyValues($rule) as $value) {
                $semesterNames[mb_strtolower($value)] = true;
            }

            return $rule;
        });

        $termNamesBySemesterName = $this->getMatchedTermNamesBySemesterName(array_keys($semesterNames));

        return $this->mapRules($rules, function (array $rule) use ($termNamesBySemesterName): array {
            $type = self::TYPES[$rule['type'] ?? ''] ?? null;

            if ($type === null) {
                return $rule;
            }

            $rule['type'] = $type;

            if (is_array($rule['data']['settings']['values'] ?? null)) {
                $rule['data']['settings']['values'] = collect($rule['data']['settings']['values'])
                    ->flatMap(fn (mixed $value): array => is_string($value) ? ($termNamesBySemesterName[mb_strtolower($value)] ?? [$value]) : [$value])
                    ->unique()
                    ->values()
                    ->all();
            }

            return $rule;
        });
    }

    /**
     * @param array<string, mixed> $rule
     *
     * @return array<string>
     */
    private function getLegacyValues(array $rule): array
    {
        if (! array_key_exists($rule['type'] ?? '', self::TYPES)) {
            return [];
        }

        $values = $rule['data']['settings']['values'] ?? null;

        return is_array($values) ? array_values(array_filter($values, is_string(...))) : [];
    }

    /**
     * @param array<string> $semesterNames
     *
     * @return array<string, array<string>>
     */
    private function getMatchedTermNamesBySemesterName(array $semesterNames): array
    {
        if (blank($semesterNames)) {
            return [];
        }

        return Enrollment::query()
            ->tap(new JoinMatchedTerm())
            ->whereIn(DB::raw('lower(enrollments.semester_name)'), $semesterNames)
            ->distinct()
            ->select([
                DB::raw('lower(enrollments.semester_name) AS semester_name'),
                DB::raw('lower(terms.name) AS term_name'),
            ])
            ->orderBy('term_name')
            ->toBase()
            ->get()
            ->groupBy('semester_name')
            ->map(fn ($rows): array => $rows->pluck('term_name')->all())
            ->all();
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
}
