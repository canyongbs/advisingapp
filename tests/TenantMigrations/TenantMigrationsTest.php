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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\SmsTemplate;
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
use Illuminate\Database\QueryException;
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

test('2026_10_23_212020_remove_enrollment_semester_auto_import_settings removes the auto import settings', function () {
    isolatedMigration(
        '2026_10_23_212020_remove_enrollment_semester_auto_import_settings',
        function () {
            $settings = [
                ['group' => 'student_information_system', 'name' => 'is_enrollment_semester_auto_import_enabled'],
                ['group' => 'student_information_system', 'name' => 'enrollment_semester_auto_import_default_order'],
            ];

            foreach ($settings as $setting) {
                assertDatabaseHas('settings', $setting);
            }

            $migrate = Artisan::call('migrate', [
                '--path' => 'app-modules/student-data-model/database/migrations/2026_10_23_212020_remove_enrollment_semester_auto_import_settings.php',
            ]);

            expect($migrate)->toBe(Command::SUCCESS);

            foreach ($settings as $setting) {
                assertDatabaseMissing('settings', $setting);
            }
        }
    );
});

// TODO: Cleanup Task (basic-needs-citext) - Delete this describe and everything contained within
describe('basic needs categories name citext change', function () {
    it('renames case-insensitive duplicate basic needs category names', function () {
        isolatedMigration(
            '2026_10_26_113602_convert_basic_needs_categories_name_to_citext',
            function () {
                // Setup data before migration
                $category1 = BasicNeedsCategory::factory()->create(['name' => 'Food Pantry', 'created_at' => now()->subMinutes(3)]);
                $category2 = BasicNeedsCategory::factory()->create(['name' => 'food pantry', 'created_at' => now()->subMinutes(2)]);
                $category3 = BasicNeedsCategory::factory()->create(['name' => 'FOOD PANTRY', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted category sharing a name with the live duplicate group must be
                // ignored by de-duplication: it should neither affect the live renumbering
                // nor get renamed itself. It uses a distinct exact string (differing only by
                // case) so the pre-migration case-sensitive unique index is not violated.
                $deletedCategory = BasicNeedsCategory::factory()->create(['name' => 'Food pantry', 'created_at' => now()->subMinutes(4)]);
                $deletedCategory->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113602_convert_basic_needs_categories_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest live record keeps its name, the rest are renumbered by their own original value
                expect($category1->refresh()->name)->toBe('Food Pantry');
                expect($category2->refresh()->name)->toBe('food pantry-2');
                expect($category3->refresh()->name)->toBe('FOOD PANTRY-3');
                // Untouched: excluded from the live dedup group entirely, despite the name collision
                expect($deletedCategory->refresh()->name)->toBe('Food pantry');
            }
        );
    });

    it('can be rolled back when a live category reuses a soft-deleted category name', function () {
        isolatedMigration(
            '2026_10_26_113602_convert_basic_needs_categories_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113602_convert_basic_needs_categories_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // Valid post-migration data: the partial unique index ignores soft-deleted rows,
                // so a live record may reuse the exact name of a soft-deleted one. down() must
                // restore a partial (not all-row) index so this data does not break the rollback.
                $deletedCategory = BasicNeedsCategory::factory()->create(['name' => 'Food Pantry']);
                $deletedCategory->delete();
                BasicNeedsCategory::factory()->create(['name' => 'Food Pantry']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113602_convert_basic_needs_categories_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });
});

// TODO: Cleanup Task (basic-needs-citext) - Delete this describe and everything contained within
describe('basic needs programs name citext change', function () {
    it('renames case-insensitive duplicate basic needs program names', function () {
        isolatedMigration(
            '2026_10_26_113604_convert_basic_needs_programs_name_to_citext',
            function () {
                // Setup data before migration
                $category = BasicNeedsCategory::factory()->create();

                $program1 = BasicNeedsProgram::factory()->create(['name' => 'Meal Support', 'basic_needs_category_id' => $category->getKey(), 'created_at' => now()->subMinutes(3)]);
                $program2 = BasicNeedsProgram::factory()->create(['name' => 'meal support', 'basic_needs_category_id' => $category->getKey(), 'created_at' => now()->subMinutes(2)]);
                $program3 = BasicNeedsProgram::factory()->create(['name' => 'MEAL SUPPORT', 'basic_needs_category_id' => $category->getKey(), 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted program sharing a name with the live duplicate group must be
                // ignored by de-duplication: it should neither affect the live renumbering
                // nor get renamed itself. It uses a distinct exact string (differing only by
                // case) so the pre-migration case-sensitive unique index is not violated.
                $deletedProgram = BasicNeedsProgram::factory()->create(['name' => 'Meal support', 'basic_needs_category_id' => $category->getKey(), 'created_at' => now()->subMinutes(4)]);
                $deletedProgram->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113604_convert_basic_needs_programs_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest live record keeps its name, the rest are renumbered by their own original value
                expect($program1->refresh()->name)->toBe('Meal Support');
                expect($program2->refresh()->name)->toBe('meal support-2');
                expect($program3->refresh()->name)->toBe('MEAL SUPPORT-3');
                // Untouched: excluded from the live dedup group entirely, despite the name collision
                expect($deletedProgram->refresh()->name)->toBe('Meal support');
            }
        );
    });

    it('can be rolled back when a live program reuses a soft-deleted program name', function () {
        isolatedMigration(
            '2026_10_26_113604_convert_basic_needs_programs_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113604_convert_basic_needs_programs_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                $category = BasicNeedsCategory::factory()->create();

                // Valid post-migration data: the partial unique index ignores soft-deleted rows,
                // so a live record may reuse the exact name of a soft-deleted one. down() must
                // restore a partial (not all-row) index so this data does not break the rollback.
                $deletedProgram = BasicNeedsProgram::factory()->create(['name' => 'Meal Support', 'basic_needs_category_id' => $category->getKey()]);
                $deletedProgram->delete();
                BasicNeedsProgram::factory()->create(['name' => 'Meal Support', 'basic_needs_category_id' => $category->getKey()]);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/basic-needs/database/migrations/2026_10_26_113604_convert_basic_needs_programs_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });
});

// TODO: Cleanup Task (campaign-name-citext) - Delete this describe and everything contained within
describe('campaigns name citext change', function () {
    it('renames case-insensitive duplicate campaign names', function () {
        isolatedMigration(
            '2026_10_27_130340_convert_campaigns_name_to_citext',
            function () {
                // Setup data before migration
                $campaign1 = Campaign::factory()->create(['name' => 'Fall Outreach', 'created_at' => now()->subMinutes(3)]);
                $campaign2 = Campaign::factory()->create(['name' => 'fall outreach', 'created_at' => now()->subMinutes(2)]);
                $campaign3 = Campaign::factory()->create(['name' => 'FALL OUTREACH', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted campaign sharing a name with the live duplicate group must be
                // ignored by de-duplication: it should neither affect the live renumbering
                // nor get renamed itself.
                $deletedCampaign = Campaign::factory()->create(['name' => 'Fall outreach', 'created_at' => now()->subMinutes(4)]);
                $deletedCampaign->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/campaign/database/migrations/2026_10_27_130340_convert_campaigns_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest live record keeps its name, the rest are renumbered by their own original value
                expect($campaign1->refresh()->name)->toBe('Fall Outreach');
                expect($campaign2->refresh()->name)->toBe('fall outreach-2');
                expect($campaign3->refresh()->name)->toBe('FALL OUTREACH-3');
                // Untouched: excluded from the live dedup group entirely, despite the name collision
                expect($deletedCampaign->refresh()->name)->toBe('Fall outreach');
            }
        );
    });

    it('can be rolled back when a live campaign reuses a soft-deleted campaign name', function () {
        isolatedMigration(
            '2026_10_27_130340_convert_campaigns_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/campaign/database/migrations/2026_10_27_130340_convert_campaigns_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // Valid post-migration data: the partial unique index ignores soft-deleted rows,
                // so a live record may reuse the exact name of a soft-deleted one. down() must
                // restore a partial (not all-row) index so this data does not break the rollback.
                $deletedCampaign = Campaign::factory()->create(['name' => 'Fall Outreach']);
                $deletedCampaign->delete();
                Campaign::factory()->create(['name' => 'Fall Outreach']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/campaign/database/migrations/2026_10_27_130340_convert_campaigns_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });

    it('does not rename a case-variant duplicate onto an existing truncated name', function () {
        isolatedMigration(
            '2026_10_27_130340_convert_campaigns_name_to_citext',
            function () {
                // Regression: a 255-char duplicate renamed with a "-2" suffix is truncated to 253
                // chars + "-2", which can land on an unrelated live row that already holds that exact
                // truncated value. The de-duplicator must detect the live collision and pick the next
                // free suffix instead of producing a name that breaks the new unique index.
                $maxLengthUpper = str_repeat('A', 255);
                $maxLengthLower = str_repeat('a', 255);
                $truncatedCollision = str_repeat('a', 253) . '-2';

                $campaign1 = Campaign::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
                $campaign2 = Campaign::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinutes(1)]);
                $campaign3 = Campaign::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/campaign/database/migrations/2026_10_27_130340_convert_campaigns_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest live record keeps its name.
                expect($campaign1->refresh()->name)->toBe($maxLengthUpper);
                // "-2" would collide with the pre-existing truncated row, so the next suffix is used.
                expect($campaign2->refresh()->name)->toBe(str_repeat('a', 253) . '-3');
                // The pre-existing truncated row is in its own group and is left untouched.
                expect($campaign3->refresh()->name)->toBe($truncatedCollision);
            }
        );
    });
});

// TODO: Cleanup Task (template-name-citext) - Delete this describe and everything contained within
describe('email templates name citext change', function (): void {
    it('renames case-insensitive duplicate email template names', function (): void {
        isolatedMigration(
            '2026_10_28_130001_convert_email_templates_name_to_citext',
            function (): void {
                $template1 = EmailTemplate::factory()->create(['name' => 'Welcome Message', 'created_at' => now()->subMinutes(3)]);
                $template2 = EmailTemplate::factory()->create(['name' => 'welcome message', 'created_at' => now()->subMinutes(2)]);
                $template3 = EmailTemplate::factory()->create(['name' => 'WELCOME MESSAGE', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted template is excluded from de-duplication and must be left untouched.
                $deletedTemplate = EmailTemplate::factory()->create(['name' => 'Welcome message', 'created_at' => now()->subMinutes(4)]);
                $deletedTemplate->delete();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130001_convert_email_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($template1->refresh()->name)->toBe('Welcome Message');
                expect($template2->refresh()->name)->toBe('welcome message-2');
                expect($template3->refresh()->name)->toBe('WELCOME MESSAGE-3');
                expect($deletedTemplate->refresh()->name)->toBe('Welcome message');

                expect(fn () => EmailTemplate::factory()->create(['name' => 'welcome MESSAGE']))
                    ->toThrow(QueryException::class);
            }
        );
    });

    it('can be rolled back when a live email template reuses a soft-deleted template name', function (): void {
        isolatedMigration(
            '2026_10_28_130001_convert_email_templates_name_to_citext',
            function (): void {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130001_convert_email_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // The partial unique index ignores soft-deleted rows, so down() must restore a
                // partial (not all-row) index for this valid post-migration data.
                EmailTemplate::factory()->create(['name' => 'Welcome Message'])->delete();
                EmailTemplate::factory()->create(['name' => 'Welcome Message']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130001_convert_email_templates_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });

    it('does not rename a case-variant duplicate onto an existing truncated name', function (): void {
        isolatedMigration(
            '2026_10_28_130001_convert_email_templates_name_to_citext',
            function (): void {
                $maxLengthUpper = str_repeat('A', 255);
                $maxLengthLower = str_repeat('a', 255);
                $truncatedCollision = str_repeat('a', 253) . '-2';

                $template1 = EmailTemplate::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
                $template2 = EmailTemplate::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinutes(1)]);
                $template3 = EmailTemplate::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130001_convert_email_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($template1->refresh()->name)->toBe($maxLengthUpper);
                expect($template2->refresh()->name)->toBe(str_repeat('a', 253) . '-3');
                expect($template3->refresh()->name)->toBe($truncatedCollision);
            }
        );
    });
});

// TODO: Cleanup Task (template-name-citext) - Delete this describe and everything contained within
describe('sms templates name citext change', function (): void {
    it('renames case-insensitive duplicate sms template names', function (): void {
        isolatedMigration(
            '2026_10_28_130002_convert_sms_templates_name_to_citext',
            function (): void {
                $template1 = SmsTemplate::factory()->create(['name' => 'Welcome Message', 'created_at' => now()->subMinutes(3)]);
                $template2 = SmsTemplate::factory()->create(['name' => 'welcome message', 'created_at' => now()->subMinutes(2)]);
                $template3 = SmsTemplate::factory()->create(['name' => 'WELCOME MESSAGE', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted template is excluded from de-duplication and must be left untouched.
                $deletedTemplate = SmsTemplate::factory()->create(['name' => 'Welcome message', 'created_at' => now()->subMinutes(4)]);
                $deletedTemplate->delete();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130002_convert_sms_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($template1->refresh()->name)->toBe('Welcome Message');
                expect($template2->refresh()->name)->toBe('welcome message-2');
                expect($template3->refresh()->name)->toBe('WELCOME MESSAGE-3');
                expect($deletedTemplate->refresh()->name)->toBe('Welcome message');

                expect(fn () => SmsTemplate::factory()->create(['name' => 'welcome MESSAGE']))
                    ->toThrow(QueryException::class);
            }
        );
    });

    it('can be rolled back when a live sms template reuses a soft-deleted template name', function (): void {
        isolatedMigration(
            '2026_10_28_130002_convert_sms_templates_name_to_citext',
            function (): void {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130002_convert_sms_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // The partial unique index ignores soft-deleted rows, so down() must restore a
                // partial (not all-row) index for this valid post-migration data.
                SmsTemplate::factory()->create(['name' => 'Welcome Message'])->delete();
                SmsTemplate::factory()->create(['name' => 'Welcome Message']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130002_convert_sms_templates_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });

    it('does not rename a case-variant duplicate onto an existing truncated name', function (): void {
        isolatedMigration(
            '2026_10_28_130002_convert_sms_templates_name_to_citext',
            function (): void {
                $maxLengthUpper = str_repeat('A', 255);
                $maxLengthLower = str_repeat('a', 255);
                $truncatedCollision = str_repeat('a', 253) . '-2';

                $template1 = SmsTemplate::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
                $template2 = SmsTemplate::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinutes(1)]);
                $template3 = SmsTemplate::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_28_130002_convert_sms_templates_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($template1->refresh()->name)->toBe($maxLengthUpper);
                expect($template2->refresh()->name)->toBe(str_repeat('a', 253) . '-3');
                expect($template3->refresh()->name)->toBe($truncatedCollision);
            }
        );
    });
});

// TODO: Cleanup Task (alert-status-name-citext) - Delete this describe and everything contained within
describe('alert statuses name citext change', function () {
    it('renames case-insensitive duplicate alert status names', function () {
        isolatedMigration(
            '2026_10_28_104435_convert_alert_statuses_name_to_citext',
            function () {
                // Setup data before migration
                $concernStatus1 = ConcernStatus::factory()->create(['name' => 'Under Review', 'created_at' => now()->subMinutes(3)]);
                $concernStatus2 = ConcernStatus::factory()->create(['name' => 'under review', 'created_at' => now()->subMinutes(2)]);
                $concernStatus3 = ConcernStatus::factory()->create(['name' => 'UNDER REVIEW', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted alert status sharing a name with the live duplicate group must be
                // ignored by de-duplication: it should neither affect the live renumbering
                // nor get renamed itself.
                $deletedConcernStatus = ConcernStatus::factory()->create(['name' => 'Under review', 'created_at' => now()->subMinutes(4)]);
                $deletedConcernStatus->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/concern/database/migrations/2026_10_28_104435_convert_alert_statuses_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest live record keeps its name, the rest are renumbered by their own original value
                expect($concernStatus1->refresh()->name)->toBe('Under Review');
                expect($concernStatus2->refresh()->name)->toBe('under review-2');
                expect($concernStatus3->refresh()->name)->toBe('UNDER REVIEW-3');
                // Untouched: excluded from the live dedup group entirely, despite the name collision
                expect($deletedConcernStatus->refresh()->name)->toBe('Under review');
            }
        );
    });

    it('can be rolled back when a live alert status reuses a soft-deleted alert status name', function () {
        isolatedMigration(
            '2026_10_28_104435_convert_alert_statuses_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/concern/database/migrations/2026_10_28_104435_convert_alert_statuses_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                // Valid post-migration data: the partial unique index ignores soft-deleted rows,
                // so a live record may reuse the exact name of a soft-deleted one.
                $deletedConcernStatus = ConcernStatus::factory()->create(['name' => 'Under Review']);
                $deletedConcernStatus->delete();
                ConcernStatus::factory()->create(['name' => 'Under Review']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/concern/database/migrations/2026_10_28_104435_convert_alert_statuses_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);
            }
        );
    });
});

// TODO: Cleanup Task (advisor-name-citext) - Delete this describe and everything contained within
describe('advisor names citext change', function () {
    it('renames case-insensitive duplicate employee advisor names and ignores soft-deleted ones', function () {
        isolatedMigration(
            '2026_10_28_164257_convert_ai_assistants_name_to_citext',
            function () {
                $advisor1 = AiAssistant::factory()->create(['name' => 'Admissions Helper', 'created_at' => now()->subMinutes(3)]);
                $advisor2 = AiAssistant::factory()->create(['name' => 'admissions helper', 'created_at' => now()->subMinutes(2)]);
                $advisor3 = AiAssistant::factory()->create(['name' => 'ADMISSIONS HELPER', 'created_at' => now()->subMinutes(1)]);

                $deletedAdvisor = AiAssistant::factory()->create(['name' => 'Admissions helper', 'created_at' => now()->subMinutes(4)]);
                $deletedAdvisor->delete();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164257_convert_ai_assistants_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($advisor1->refresh()->name)->toBe('Admissions Helper');
                expect($advisor2->refresh()->name)->toBe('admissions helper-2');
                expect($advisor3->refresh()->name)->toBe('ADMISSIONS HELPER-3');
                expect(AiAssistant::withTrashed()->findOrFail($deletedAdvisor->getKey())->name)->toBe('Admissions helper');
            }
        );
    });

    it('can roll back the employee advisor migration when a live advisor reuses a soft-deleted name', function () {
        isolatedMigration(
            '2026_10_28_164257_convert_ai_assistants_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164257_convert_ai_assistants_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                AiAssistant::factory()->create(['name' => 'Admissions Helper'])->delete();
                AiAssistant::factory()->create(['name' => 'Admissions Helper']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164257_convert_ai_assistants_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);

                // The rolled-back schema must still support running the migration again.
                $remigrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164257_convert_ai_assistants_name_to_citext.php']);

                expect($remigrate)->toBe(Command::SUCCESS);
            }
        );
    });

    it('does not rename a case-variant duplicate employee advisor onto an existing truncated name', function () {
        isolatedMigration(
            '2026_10_28_164257_convert_ai_assistants_name_to_citext',
            function () {
                $maxLengthUpper = str_repeat('A', 255);
                $maxLengthLower = str_repeat('a', 255);
                $truncatedCollision = str_repeat('a', 253) . '-2';

                $advisor1 = AiAssistant::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
                $advisor2 = AiAssistant::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinutes(1)]);
                $advisor3 = AiAssistant::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164257_convert_ai_assistants_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($advisor1->refresh()->name)->toBe($maxLengthUpper);
                expect($advisor2->refresh()->name)->toBe(str_repeat('a', 253) . '-3');
                expect($advisor3->refresh()->name)->toBe($truncatedCollision);
            }
        );
    });

    it('renames case-insensitive duplicate customer advisor names and ignores soft-deleted ones', function () {
        isolatedMigration(
            '2026_10_28_164258_convert_customer_advisors_name_to_citext',
            function () {
                $advisor1 = CustomerAdvisor::factory()->create(['name' => 'Admissions Helper', 'created_at' => now()->subMinutes(3)]);
                $advisor2 = CustomerAdvisor::factory()->create(['name' => 'admissions helper', 'created_at' => now()->subMinutes(2)]);
                $advisor3 = CustomerAdvisor::factory()->create(['name' => 'ADMISSIONS HELPER', 'created_at' => now()->subMinutes(1)]);

                $deletedAdvisor = CustomerAdvisor::factory()->create(['name' => 'Admissions helper', 'created_at' => now()->subMinutes(4)]);
                $deletedAdvisor->delete();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164258_convert_customer_advisors_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($advisor1->refresh()->name)->toBe('Admissions Helper');
                expect($advisor2->refresh()->name)->toBe('admissions helper-2');
                expect($advisor3->refresh()->name)->toBe('ADMISSIONS HELPER-3');
                expect(CustomerAdvisor::withTrashed()->findOrFail($deletedAdvisor->getKey())->name)->toBe('Admissions helper');
            }
        );
    });

    it('can roll back the customer advisor migration when a live advisor reuses a soft-deleted name', function () {
        isolatedMigration(
            '2026_10_28_164258_convert_customer_advisors_name_to_citext',
            function () {
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164258_convert_customer_advisors_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                CustomerAdvisor::factory()->create(['name' => 'Admissions Helper'])->delete();
                CustomerAdvisor::factory()->create(['name' => 'Admissions Helper']);

                $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164258_convert_customer_advisors_name_to_citext.php']);

                expect($rollback)->toBe(Command::SUCCESS);

                // The rolled-back schema must still support running the migration again.
                $remigrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164258_convert_customer_advisors_name_to_citext.php']);

                expect($remigrate)->toBe(Command::SUCCESS);
            }
        );
    });

    it('does not rename a case-variant duplicate customer advisor onto an existing truncated name', function () {
        isolatedMigration(
            '2026_10_28_164258_convert_customer_advisors_name_to_citext',
            function () {
                $maxLengthUpper = str_repeat('A', 255);
                $maxLengthLower = str_repeat('a', 255);
                $truncatedCollision = str_repeat('a', 253) . '-2';

                $advisor1 = CustomerAdvisor::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
                $advisor2 = CustomerAdvisor::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinutes(1)]);
                $advisor3 = CustomerAdvisor::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_10_28_164258_convert_customer_advisors_name_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($advisor1->refresh()->name)->toBe($maxLengthUpper);
                expect($advisor2->refresh()->name)->toBe(str_repeat('a', 253) . '-3');
                expect($advisor3->refresh()->name)->toBe($truncatedCollision);
            }
        );
    });
});
