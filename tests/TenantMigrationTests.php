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
use AdvisingApp\Group\Models\Group;
use App\Enums\TagType;
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

// TODO: Cleanup Task GroupCitextCleanup - Delete this describe and everything contained within
describe('segment name citext change', function () {
    it('renames case-insensitive duplicate group names', function () {
        isolatedMigration(
            '2026_09_02_142139_convert_segments_name_to_citext',
            function () {
                // Setup data before migration
                $group1 = Group::factory()->create(['name' => 'Group name']);
                $group2 = Group::factory()->create(['name' => 'group Name']);
                $group3 = Group::factory()->create(['name' => 'group name']);

                // Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/group/database/migrations/2026_09_02_142139_convert_segments_name_to_citext.php']);

                // Confirm migration ran successfully
                expect($migrate)->toBe(Command::SUCCESS);

                // Add any assertions to verify the migration's effects
                expect($group1->refresh()->name)->toBe('Group name');
                expect($group2->refresh()->name)->toBe('group Name-2');
                expect($group3->refresh()->name)->toBe('group name-3');
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

// TODO: Cleanup Task PromptCiTextCleanup - Delete this describe and everything contained within
describe('prompt type title citext change', function () {
    it('deduplicates case-insensitive prompt type titles before converting the column', function () {
        isolatedMigration(
            '2026_09_02_181730_convert_prompt_types_title_to_citext',
            function () {
                $promptType1 = (string) Str::uuid();
                $promptType2 = (string) Str::uuid();
                $promptType3 = (string) Str::uuid();

                DB::table('prompt_types')->insert([
                    ['id' => $promptType1, 'title' => 'Prompt Type', 'created_at' => now()->subMinutes(3), 'updated_at' => now()],
                    ['id' => $promptType2, 'title' => 'prompt type', 'created_at' => now()->subMinutes(2), 'updated_at' => now()],
                    ['id' => $promptType3, 'title' => 'PROMPT TYPE', 'created_at' => now()->subMinutes(1), 'updated_at' => now()],
                ]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_09_02_181730_convert_prompt_types_title_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect(DB::table('prompt_types')->where('id', $promptType1)->value('title'))->toBe('Prompt Type');
                expect(DB::table('prompt_types')->where('id', $promptType2)->value('title'))->toBe('prompt type-2');
                expect(DB::table('prompt_types')->where('id', $promptType3)->value('title'))->toBe('PROMPT TYPE-3');
            }
        );
    });
});
// TODO: Cleanup Task PromptCiTextCleanup - Delete this describe and everything contained within
describe('prompt title citext change', function () {
    it('deduplicates case-insensitive prompt titles scoped per type before converting the column', function () {
        isolatedMigration(
            '2026_09_02_181821_convert_prompts_title_to_citext',
            function () {
                $typeId = (string) Str::uuid();
                $otherTypeId = (string) Str::uuid();

                DB::table('prompt_types')->insert([
                    ['id' => $typeId, 'title' => 'Type A', 'created_at' => now(), 'updated_at' => now()],
                    ['id' => $otherTypeId, 'title' => 'Type B', 'created_at' => now(), 'updated_at' => now()],
                ]);

                $prompt1 = (string) Str::uuid();
                $prompt2 = (string) Str::uuid();
                $prompt3 = (string) Str::uuid();
                $promptOtherType = (string) Str::uuid();

                DB::table('prompts')->insert([
                    ['id' => $prompt1, 'title' => 'Prompt', 'prompt' => 'Prompt body', 'type_id' => $typeId, 'created_at' => now()->subMinutes(3), 'updated_at' => now()],
                    ['id' => $prompt2, 'title' => 'prompt', 'prompt' => 'Prompt body', 'type_id' => $typeId, 'created_at' => now()->subMinutes(2), 'updated_at' => now()],
                    ['id' => $prompt3, 'title' => 'PROMPT', 'prompt' => 'Prompt body', 'type_id' => $typeId, 'created_at' => now()->subMinutes(1), 'updated_at' => now()],
                    ['id' => $promptOtherType, 'title' => 'PrOmPt', 'prompt' => 'Prompt body', 'type_id' => $otherTypeId, 'created_at' => now(), 'updated_at' => now()],
                ]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_09_02_181821_convert_prompts_title_to_citext.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect(DB::table('prompts')->where('id', $prompt1)->value('title'))->toBe('Prompt');
                expect(DB::table('prompts')->where('id', $prompt2)->value('title'))->toBe('prompt-2');
                expect(DB::table('prompts')->where('id', $prompt3)->value('title'))->toBe('PROMPT-3');

                expect(DB::table('prompts')->where('id', $promptOtherType)->value('title'))->toBe('PrOmPt');
            }
        );
    });
});
