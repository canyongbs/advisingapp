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

use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\MeetingCenter\Models\Event;
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


                // A soft-deleted event sharing a title must be ignored by de-duplication
                $deletedEvent = Event::factory()->create(['title' => 'Deleted event']);
                $deletedEvent->delete();

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/meeting-center/database/migrations/2026_09_02_120000_convert_events_title_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // Add any assertions to verify the migration's effects
                expect($event1->refresh()->title)->toBe('Event title');
                expect($event2->refresh()->title)->toBe('event Title-2');
                expect($event3->refresh()->title)->toBe('event title-3');
                expect($deletedEvent->refresh()->title)->toBe('Deleted event');
            }
        );
    });
});
