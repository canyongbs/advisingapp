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

use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use App\Enums\TagType;
use App\Models\SystemUser;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

describe('survey name citext change', function () {
    it('deduplicates case-insensitive survey names before converting the column', function () {
        isolatedMigration(
            '2026_09_01_122143_convert_surveys_name_to_citext',
            function () {
                // Setup data before migration. A plain, case-sensitive unique index
                // allows these three names to coexist prior to the citext conversion.
                $survey1 = (string) Str::uuid();
                $survey2 = (string) Str::uuid();
                $survey3 = (string) Str::uuid();

                DB::table('surveys')->insert([
                    ['id' => $survey1, 'name' => 'Survey', 'created_at' => now()->subMinutes(3), 'updated_at' => now()],
                    ['id' => $survey2, 'name' => 'survey', 'created_at' => now()->subMinutes(2), 'updated_at' => now()],
                    ['id' => $survey3, 'name' => 'SURVEY', 'created_at' => now()->subMinutes(1), 'updated_at' => now()],
                ]);

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/survey/database/migrations/2026_09_01_122143_convert_surveys_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The oldest record keeps its name, later duplicates are suffixed.
                expect(DB::table('surveys')->where('id', $survey1)->value('name'))->toBe('Survey');
                expect(DB::table('surveys')->where('id', $survey2)->value('name'))->toBe('survey-2');
                expect(DB::table('surveys')->where('id', $survey3)->value('name'))->toBe('SURVEY-3');
            }
        );
    });
});

// TODO: Cleanup Task EventCitextCleanup - Delete this describe and everything contained within
describe('event title citext change', function () {
    it('renames case-insensitive duplicate event titles', function () {
        isolatedMigration(
            '2026_09_02_120000_convert_events_title_to_citext',
            function () {
                // Setup data before migration

                $event1 = Event::factory()->create(['title' => 'Event title', 'created_at' => now()->subMinutes(3)]);
                $event2 = Event::factory()->create(['title' => 'event Title', 'created_at' => now()->subMinutes(2)]);
                $event3 = Event::factory()->create(['title' => 'event title', 'created_at' => now()->subMinutes(1)]);

                // A soft-deleted event sharing a title with the live duplicate group must be
                // ignored by de-duplication: it should neither affect the live renumbering
                // nor get renamed itself.
                $deletedEvent = Event::factory()->create(['title' => 'event title', 'created_at' => now()->subMinutes(4)]);
                $deletedEvent->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/meeting-center/database/migrations/2026_09_02_120000_convert_events_title_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // Add any assertions to verify the migration's effects
                expect($event1->refresh()->title)->toBe('Event title');
                expect($event2->refresh()->title)->toBe('event Title-2');
                expect($event3->refresh()->title)->toBe('event title-3');
                // Untouched: excluded from the live dedup group entirely, despite the title collision
                expect($deletedEvent->refresh()->title)->toBe('event title');
            }
        );
    });
});

// TODO: Cleanup Task RoleCitextCleanup - Delete this describe and everything contained within
describe('role citext change', function () {
    it('properly deduplicates role names case insensitively per guard', function () {
        isolatedMigration(
            '2026_09_02_125430_convert_role_name_to_citext',
            function () {
                // Setup data before migration
                $role1 = Role::factory()->create(['name' => 'Role', 'guard_name' => 'web']);
                $role2 = Role::factory()->create(['name' => 'role', 'guard_name' => 'web']);
                $role3 = Role::factory()->create(['name' => 'ROLE', 'guard_name' => 'web']);
                // A matching name under a different guard must be left untouched
                $role4 = Role::factory()->create(['name' => 'role', 'guard_name' => 'api']);

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/authorization/database/migrations/2026_09_02_125430_convert_role_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // The first record keeps its name, the rest are suffixed within the guard
                expect($role1->refresh()->name)->toBe('Role');
                expect($role2->refresh()->name)->toBe('role-2');
                expect($role3->refresh()->name)->toBe('ROLE-3');
                expect($role4->refresh()->name)->toBe('role');
            }
        );
    });
});

// TODO: Cleanup Task ResourceHubCitextCleanup - Delete this describe and everything contained within
describe('resource hub citext change', function () {
    it('properly changes article titles', function () {
        isolatedMigration(
            '2026_09_01_220856_convert_resource_hub_article_title_to_citext',
            function () {
                // Setup data before migration
                $article1 = ResourceHubArticle::factory(['title' => 'Test Article'])->create();
                $article2 = ResourceHubArticle::factory(['title' => 'Test Article'])->create();
                $article3 = ResourceHubArticle::factory(['title' => 'Test Article'])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_09_01_220856_convert_resource_hub_article_title_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($article1->refresh()->title)->toBe('Test Article');
                expect($article2->refresh()->title)->toBe('Test Article-2');
                expect($article3->refresh()->title)->toBe('Test Article-3');
            }
        );
    });

    it('properly changes category names', function () {
        isolatedMigration(
            '2026_09_02_034833_convert_resource_hub_categories_name_to_citext',
            function () {
                // Setup data before migration
                $category1 = ResourceHubCategory::factory(['name' => 'Test Category'])->create();
                $category2 = ResourceHubCategory::factory(['name' => 'Test Category'])->create();
                $category3 = ResourceHubCategory::factory(['name' => 'Test Category'])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_09_02_034833_convert_resource_hub_categories_name_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($category1->refresh()->name)->toBe('Test Category');
                expect($category2->refresh()->name)->toBe('Test Category-2');
                expect($category3->refresh()->name)->toBe('Test Category-3');
            }
        );
    });

    it('properly changes quality names', function () {
        isolatedMigration(
            '2026_09_02_034854_convert_resource_hub_qualities_name_to_citext',
            function () {
                // Setup data before migration
                $quality1 = ResourceHubQuality::factory(['name' => 'Test Quality'])->create();
                $quality2 = ResourceHubQuality::factory(['name' => 'Test Quality'])->create();
                $quality3 = ResourceHubQuality::factory(['name' => 'Test Quality'])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_09_02_034854_convert_resource_hub_qualities_name_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($quality1->refresh()->name)->toBe('Test Quality');
                expect($quality2->refresh()->name)->toBe('Test Quality-2');
                expect($quality3->refresh()->name)->toBe('Test Quality-3');
            }
        );
    });

    it('properly changes status names', function () {
        isolatedMigration(
            '2026_09_02_034904_convert_resource_hub_statuses_name_to_citext',
            function () {
                // Setup data before migration
                $status1 = ResourceHubStatus::factory(['name' => 'Test Status'])->create();
                $status2 = ResourceHubStatus::factory(['name' => 'Test Status'])->create();
                $status3 = ResourceHubStatus::factory(['name' => 'Test Status'])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_09_02_034904_convert_resource_hub_statuses_name_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($status1->refresh()->name)->toBe('Test Status');
                expect($status2->refresh()->name)->toBe('Test Status-2');
                expect($status3->refresh()->name)->toBe('Test Status-3');
            }
        );
    });
});

// TODO: Cleanup Task TagCitextCleanup - Delete this describe and everything contained within
describe('tag citext change', function () {
    it('properly changes tag names', function () {
        isolatedMigration(
            '2026_09_02_135528_convert_tag_name_to_citext',
            function () {
                // Setup data before migration
                $studentTag1 = Tag::factory(['name' => 'Student Tag', 'type' => TagType::Student])->create();
                $prospectTag1 = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
                $studentTag2 = Tag::factory(['name' => 'Student Tag', 'type' => TagType::Student])->create();
                $prospectTag2 = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
                $studentTag3 = Tag::factory(['name' => 'Student Tag', 'type' => TagType::Student])->create();
                $prospectTag3 = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_09_02_135528_convert_tag_name_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($studentTag1->refresh()->name)->toBe('Student Tag');
                expect($prospectTag1->refresh()->name)->toBe('Prospect Tag');
                expect($studentTag2->refresh()->name)->toBe('Student Tag-2');
                expect($prospectTag2->refresh()->name)->toBe('Prospect Tag-2');
                expect($studentTag3->refresh()->name)->toBe('Student Tag-3');
                expect($prospectTag3->refresh()->name)->toBe('Prospect Tag-3');
            }
        );
    });
});

// TODO: Cleanup Task SystemUserCitextCleanup - Delete this describe and everything contained within
describe('system user citext change', function () {
    it('properly changes system user names', function () {
        isolatedMigration(
            '2026_09_02_062437_convert_system_user_name_to_citext',
            function () {
                // Setup data before migration
                $systemUser1 = SystemUser::factory(['name' => 'System user'])->create();
                $systemUser2 = SystemUser::factory(['name' => 'system User'])->create();
                $systemUser3 = SystemUser::factory(['name' => 'system user'])->create();
                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_09_02_062437_convert_system_user_name_to_citext.php']);
                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);
                // Add any assertions to verify the migration's effects
                expect($systemUser1->refresh()->name)->toBe('System user');
                expect($systemUser2->refresh()->name)->toBe('system User-2');
                expect($systemUser3->refresh()->name)->toBe('system user-3');
            }
        );
    });
});
