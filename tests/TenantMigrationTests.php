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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

            $engagementWithSource = Engagement::factory()->createQuietly([
                'campaign_action_id' => $action->id,
            ]);

            $engagementWithoutSource = Engagement::factory()->createQuietly([
                'campaign_action_id' => null,
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
