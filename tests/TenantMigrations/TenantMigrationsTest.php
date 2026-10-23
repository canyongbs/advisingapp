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

use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Features\StudentGroupFilterConsolidationFeature;
use App\Features\TermsConsolidationFeature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

// Add tests for migration files here

// Example migration test, leave commented out for future use as a template/example
//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});

test('2026_04_08_145038_rename_campaign_action_id_to_source_morph_on_engagements_table renames column and backfills source_type', function () {
    isolatedMigration(
        '2026_04_08_145038_rename_campaign_action_id_to_source_morph_on_engagements_table',
        function () {
            $action = CampaignAction::factory()->createQuietly();

            // Create a prospect directly to avoid triggering ProspectFactory's
            // afterCreating callback which creates PhoneNumberLookup records
            // (the phone_number_lookups table does not yet exist at this migration point).
            $prospectId = (string) Str::uuid();
            $statusId = (string) Str::uuid();
            DB::table('prospect_statuses')->insertOrIgnore([
                'id' => $statusId,
                'classification' => 'new',
                'name' => 'New',
                'color' => 'primary',
                'sort' => 1,
                'is_system_protected' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sourceId = (string) Str::uuid();
            DB::table('prospect_sources')->insertOrIgnore([
                'id' => $sourceId,
                'name' => 'Test Source',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('prospects')->insert([
                'id' => $prospectId,
                'status_id' => $statusId,
                'source_id' => $sourceId,
                'first_name' => 'Test',
                'last_name' => 'Prospect',
                'full_name' => 'Test Prospect',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $engagementWithSource = Engagement::factory()->createQuietly([
                'campaign_action_id' => $action->id,
                'recipient_type' => 'prospect',
                'recipient_id' => $prospectId,
            ]);

            $engagementWithoutSource = Engagement::factory()->createQuietly([
                'campaign_action_id' => null,
                'recipient_type' => 'prospect',
                'recipient_id' => $prospectId,
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_04_08_145038_rename_campaign_action_id_to_source_morph_on_engagements_table.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $withSource = DB::table('engagements')->where('id', $engagementWithSource->id)->first();

            expect($withSource->source_id)->toBe($action->id); /** @phpstan-ignore-line */
            expect($withSource->source_type)->toBe('campaign_action'); /** @phpstan-ignore-line */
            $withoutSource = DB::table('engagements')->where('id', $engagementWithoutSource->id)->first();

            expect($withoutSource->source_id)->toBeNull(); /** @phpstan-ignore-line */
            expect($withoutSource->source_type)->toBeNull(); /** @phpstan-ignore-line */
        }
    );
});

test('2026_10_06_234840_tmp_backfill_ses_event_occurred_at_from_payload uses the SES send time for events without their own timestamp', function () {
    isolatedMigration(
        '2026_10_06_234840_tmp_backfill_ses_event_occurred_at_from_payload',
        function () {
            $emailMessage = EmailMessage::factory()->create();

            $payload = ['mail' => ['timestamp' => '2026-01-02T03:04:05.000Z']];

            $eventsWithoutTimestamps = collect([
                EmailMessageEventType::Send,
                EmailMessageEventType::Reject,
                EmailMessageEventType::RenderingFailure,
            ])->map(fn (EmailMessageEventType $type) => $emailMessage->events()->create([
                'type' => $type,
                'payload' => $payload,
                'occurred_at' => now(),
            ]));

            $delivery = $emailMessage->events()->create([
                'type' => EmailMessageEventType::Delivery,
                'payload' => $payload,
                'occurred_at' => '2026-05-06 07:08:09',
            ]);

            $migrate = Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_10_06_234840_tmp_backfill_ses_event_occurred_at_from_payload.php',
            ]);

            expect($migrate)->toBe(Command::SUCCESS)
                ->and($eventsWithoutTimestamps->map(fn (EmailMessageEvent $event): string => $event->refresh()->occurred_at->toDateTimeString())->unique()->all())
                ->toBe(['2026-01-02 03:04:05'])
                ->and($delivery->refresh()->occurred_at->toDateTimeString())->toBe('2026-05-06 07:08:09');
        }
    );
});

test('2026_10_12_154211_tmp_consolidate_student_group_filters deletes groups using removed filters and merges name filters', function () {
    isolatedMigration(
        '2026_10_12_154211_tmp_consolidate_student_group_filters',
        function () {
            $rule = fn (string $type, string $operator, string $text): array => [
                'type' => $type,
                'data' => ['operator' => $operator, 'settings' => ['text' => $text]],
            ];
            $orBlock = fn (array ...$groups): array => [
                'type' => 'or',
                'data' => ['groups' => array_map(fn (array $rules): array => ['rules' => $rules], $groups)],
            ];
            $createGroup = fn (GroupModel $model, ?array $rules, GroupType $type = GroupType::Dynamic): Group => Group::factory()->create([
                'model' => $model,
                'type' => $type,
                'filters' => $rules === null ? null : ['queryBuilder' => ['rules' => $rules]],
            ]);

            $topLevelRemoved = $createGroup(GroupModel::Student, [
                'a' => $rule('last', 'contains', 'Smith'),
                'b' => $rule('sisid', 'equals', '123'),
            ]);
            $campaign = Campaign::factory()->for($topLevelRemoved, 'group')->create();

            $nestedRemoved = $createGroup(GroupModel::Student, [
                'a' => $orBlock(
                    ['b' => $rule('holds', 'contains', 'X')],
                    ['c' => $orBlock(['d' => $rule('enrollmentSisid', 'equals', '123')])],
                ),
            ]);

            $tagsRule = [
                'type' => 'tags',
                'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) Str::uuid()]]],
            ];
            $legacyRules = [
                'a' => $rule('last', 'contains', 'Smith'),
                'b' => $orBlock(
                    ['c' => $rule('first', 'startsWith.inverse', 'A'), 'd' => $tagsRule],
                    ['e' => $orBlock(['f' => $rule('full_name', 'equals', 'Jo Bloggs')], ['g' => $rule('preferred', 'endsWith', 'y')])],
                ),
            ];
            $merged = $createGroup(GroupModel::Student, $legacyRules);

            $prospectFilters = [
                'a' => $rule('phone', 'contains', '555'),
                'b' => $rule('full_name', 'contains', 'Smith'),
            ];
            $prospect = $createGroup(GroupModel::Prospect, $prospectFilters);
            $staticFilters = ['a' => $rule('sisid', 'equals', '123')];
            $static = $createGroup(GroupModel::Student, $staticFilters, GroupType::Static);
            $withoutFilters = $createGroup(GroupModel::Student, null);

            expect(StudentGroupFilterConsolidationFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/group/database/migrations/2026_10_12_154211_tmp_consolidate_student_group_filters.php']);

            $nameRule = fn (string $attribute, string $operator, string $text): array => [
                'type' => 'name',
                'data' => ['operator' => $attribute, 'settings' => ['operator' => $operator, 'text' => $text]],
            ];

            expect($migrate)->toBe(Command::SUCCESS)
                ->and($merged->refresh()->filters['queryBuilder']['rules'])->toBe([
                    'a' => $nameRule('lastName', 'contains', 'Smith'),
                    'b' => $orBlock(
                        ['c' => $nameRule('firstName', 'startsWith.inverse', 'A'), 'd' => $tagsRule],
                        ['e' => $orBlock(['f' => $nameRule('fullName', 'equals', 'Jo Bloggs')], ['g' => $nameRule('preferredName', 'endsWith', 'y')])],
                    ),
                ])
                ->and($prospect->refresh()->filters['queryBuilder']['rules'])->toBe($prospectFilters)
                ->and($static->refresh()->filters['queryBuilder']['rules'])->toBe($staticFilters)
                ->and($withoutFilters->refresh()->filters)->toBeNull()
                ->and(StudentGroupFilterConsolidationFeature::active())->toBeTrue();

            assertSoftDeleted($topLevelRemoved);
            assertSoftDeleted($campaign);
            assertSoftDeleted($nestedRemoved);
            assertNotSoftDeleted($merged);
            assertNotSoftDeleted($prospect);
            assertNotSoftDeleted($static);
            assertNotSoftDeleted($withoutFilters);

            $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/group/database/migrations/2026_10_12_154211_tmp_consolidate_student_group_filters.php']);

            expect($rollback)->toBe(Command::SUCCESS)
                ->and($merged->refresh()->filters['queryBuilder']['rules'])->toBe($legacyRules)
                ->and(StudentGroupFilterConsolidationFeature::active())->toBeFalse();
        }
    );
});

test('2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms copies the enrollment semesters into terms when there are no SIS terms', function () {
    isolatedMigration(
        '2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms',
        function () {
            $semester = EnrollmentSemester::factory()->create(['name' => 'Fall 2024', 'order' => 3]);
            $deletedSemester = EnrollmentSemester::factory()->create(['deleted_at' => now()]);

            assertDatabaseCount(Term::class, 0);

            $migrate = Artisan::call('migrate', [
                '--path' => 'app-modules/student-data-model/database/migrations/2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms.php',
            ]);

            expect($migrate)->toBe(Command::SUCCESS)
                ->and(TermsConsolidationFeature::active())->toBeTrue();

            assertDatabaseCount(Term::class, 1);
            assertDatabaseHas(Term::class, [
                'id' => $semester->getKey(),
                'sis_term_id' => null,
                'name' => 'Fall 2024',
                'order' => 3,
                'is_active' => true,
            ]);
            assertDatabaseMissing(Term::class, ['id' => $deletedSemester->getKey()]);
        }
    );
});

test('2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms does not copy the enrollment semesters when there are SIS terms', function () {
    isolatedMigration(
        '2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms',
        function () {
            $sisTerm = Term::factory()->create();
            EnrollmentSemester::factory()->create();

            $migrate = Artisan::call('migrate', [
                '--path' => 'app-modules/student-data-model/database/migrations/2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms.php',
            ]);

            expect($migrate)->toBe(Command::SUCCESS)
                ->and(Term::query()->pluck('id')->all())->toBe([$sisTerm->getKey()])
                ->and(TermsConsolidationFeature::active())->toBeTrue();
        }
    );
});
