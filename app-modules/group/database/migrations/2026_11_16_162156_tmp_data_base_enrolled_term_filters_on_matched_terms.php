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

use AdvisingApp\Alert\Actions\GenerateStudentAlertsView;
use App\Features\MatchedEnrolledTermsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * The enrolled term constraint types that compared enrollment semester names, keyed to the types that compare matched term names.
     *
     * @var array<string, string>
     */
    private const TYPES = [
        'firstEnrollmentTerm.semester_name' => 'firstEnrolledTerm',
        'mostRecentEnrollmentTerm.semester_name' => 'mostRecentEnrolledTerm',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            $termNamesBySemesterName = $this->getMatchedTermNamesBySemesterName();

            $this->eachStudentGroupWithRules(function (object $group, array $filters) use ($termNamesBySemesterName): void {
                $filters['queryBuilder']['rules'] = $this->mapRules($filters['queryBuilder']['rules'], function (array $rule) use ($termNamesBySemesterName): array {
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

                $this->updateFiltersIfChanged($group, $filters);
            });

            MatchedEnrolledTermsFeature::activate();

            $this->regenerateStudentAlertsViewIfNewStudentAlertIsEnabled();
        });
    }

    /**
     * The values are not changed back to semester names, as which semester name each term name came from is not kept.
     */
    public function down(): void
    {
        DB::transaction(function (): void {
            MatchedEnrolledTermsFeature::deactivate();

            $legacyTypes = array_flip(self::TYPES);

            $this->eachStudentGroupWithRules(function (object $group, array $filters) use ($legacyTypes): void {
                $filters['queryBuilder']['rules'] = $this->mapRules($filters['queryBuilder']['rules'], function (array $rule) use ($legacyTypes): array {
                    $legacyType = $legacyTypes[$rule['type'] ?? ''] ?? null;

                    if ($legacyType !== null) {
                        $rule['type'] = $legacyType;
                    }

                    return $rule;
                });

                $this->updateFiltersIfChanged($group, $filters);
            });

            $this->regenerateStudentAlertsViewIfNewStudentAlertIsEnabled();
        });
    }

    /**
     * The lowercased names of the terms that enrollments are matched to, keyed by the lowercased semester names saved in groups' filters.
     *
     * Semester names saved before terms were matched are often the same as a term name, but not always (e.g. an enrollment matched by SIS term ID),
     * so each is replaced with the names of the terms its enrollments are matched to. A semester name no enrollment is matched by is kept.
     *
     * @return array<string, array<string>>
     */
    private function getMatchedTermNamesBySemesterName(): array
    {
        $semesterNames = [];

        $this->eachStudentGroupWithRules(function (object $group, array $filters) use (&$semesterNames): void {
            $this->mapRules($filters['queryBuilder']['rules'], function (array $rule) use (&$semesterNames): array {
                if (array_key_exists($rule['type'] ?? '', self::TYPES) && is_array($rule['data']['settings']['values'] ?? null)) {
                    foreach ($rule['data']['settings']['values'] as $value) {
                        if (is_string($value)) {
                            $semesterNames[mb_strtolower($value)] = true;
                        }
                    }
                }

                return $rule;
            });
        });

        if (blank($semesterNames)) {
            return [];
        }

        return collect(array_chunk(array_keys($semesterNames), 1000))
            ->flatMap(fn (array $semesterNamesChunk): Collection => DB::table('enrollments')
                ->join('enrollment_terms', 'enrollment_terms.enrollment_id', '=', 'enrollments.id')
                ->join('terms', 'terms.id', '=', 'enrollment_terms.term_id')
                ->whereNull('enrollments.deleted_at')
                ->whereIn(DB::raw('lower(enrollments.semester_name)'), $semesterNamesChunk)
                ->distinct()
                ->select([
                    DB::raw('lower(enrollments.semester_name) AS semester_name'),
                    DB::raw('lower(terms.name) AS term_name'),
                ])
                ->orderBy('term_name')
                ->get())
            ->groupBy('semester_name')
            ->map(fn (Collection $rows): array => $rows->pluck('term_name')->all())
            ->all();
    }

    /**
     * The New Student alert's query is part of the generated `student_alerts` view, so the view is regenerated for it to count matched terms.
     */
    private function regenerateStudentAlertsViewIfNewStudentAlertIsEnabled(): void
    {
        $isNewStudentAlertEnabled = DB::table('alert_configurations')
            ->where('preset', 'new_student')
            ->where('is_enabled', true)
            ->exists();

        if ($isNewStudentAlertEnabled) {
            app(GenerateStudentAlertsView::class)->execute();
        }
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
};
