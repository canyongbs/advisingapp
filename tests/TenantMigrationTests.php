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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Models\CustomerAdvisorMessage;
use AdvisingApp\Ai\Models\CustomerAdvisorQuestion;
use AdvisingApp\Ai\Models\CustomerAdvisorThread;
use AdvisingApp\Ai\Settings\AiCustomerAdvisorSettings;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\Engagement;
use App\Models\User;
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

describe('2026_05_12_222040_rename_qna_advisors_table_and_columns_to_customer_advisors', function () {
    it('properly updates existing qna advisors and related models', function () {
        isolatedMigration(
            '2026_05_12_222040_rename_qna_advisors_table_and_columns_to_customer_advisors',
            function () {
                // Setup data
                $user = User::factory()->create();

                $user->givePermissionTo('qna_advisor.view-any');
                $user->givePermissionTo('qna_advisor.create');
                $user->givePermissionTo('qna_advisor.*.delete');
                $user->givePermissionTo('qna_advisor.*.force-delete');
                $user->givePermissionTo('qna_advisor.*.restore');
                $user->givePermissionTo('qna_advisor.*.update');
                $user->givePermissionTo('qna_advisor.*.view');
                $user->givePermissionTo('qna_advisor_embed.view-any');
                $user->givePermissionTo('qna_advisor_embed.*.view');

                $advisor = CustomerAdvisor::factory()->create();
                $category = CustomerAdvisorCategory::factory()->for($advisor, 'customerAdvisor')->create();
                $link = CustomerAdvisorLink::factory()->create();
                $message = CustomerAdvisorMessage::factory()->create();
                $question = CustomerAdvisorQuestion::factory()->create();
                $thread = CustomerAdvisorThread::factory()->create();

                $file = new CustomerAdvisorFile();
                $file->advisor()->associate($advisor);

                $settings = app(AiCustomerAdvisorSettings::class);
                $settings->preselected_model = AiModel::Test;
                $settings->instructions = 'Test instructions';
                $settings->background_information = 'Test background information';
                $settings->restrictions = 'Test restrictions';
                $settings->save();

                expect($advisor->getTable())->toBe('qna_advisors');
                expect($category->getTable())->toBe('qna_advisor_categories');
                expect($file->getTable())->toBe('qna_advisor_files');
                expect($link->getTable())->toBe('qna_advisor_links');
                expect($message->getTable())->toBe('qna_advisor_messages');
                expect($question->getTable())->toBe('qna_advisor_questions');
                expect($thread->getTable())->toBe('qna_advisor_threads');

                expect($category->customerAdvisor->getKey())->toBe($advisor->getKey());

                expect(DB::table('permission_groups')->where('name', 'QnA Advisor')->exists())->toBeTrue();
                expect(DB::table('permission_groups')->where('name', 'QnA Advisor Embed')->exists())->toBeTrue();

                expect(DB::table('settings')->where('group', 'ai-qna-advisor')->exists())->toBeTrue();

                // Run migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/ai/database/migrations/2026_05_12_222040_rename_qna_advisors_table_and_columns_to_customer_advisors.php']);

                // Verify changes
                expect($migrate)->toBe(Command::SUCCESS);

                expect($advisor->getTable())->toBe('customer_advisors');
                expect($category->getTable())->toBe('customer_advisor_categories');
                expect($file->getTable())->toBe('customer_advisor_files');
                expect($link->getTable())->toBe('customer_advisor_links');
                expect($message->getTable())->toBe('customer_advisor_messages');
                expect($question->getTable())->toBe('customer_advisor_questions');
                expect($thread->getTable())->toBe('customer_advisor_threads');

                expect($category->customerAdvisor->getKey())->toBe($advisor->getKey());

                $user->refresh();

                expect($user->hasPermissionTo('qna_advisor.view-any'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.create'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.*.delete'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.*.force-delete'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.*.restore'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.*.update'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor.*.view'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor_embed.view-any'))->toBeFalse();
                expect($user->hasPermissionTo('qna_advisor_embed.*.view'))->toBeFalse();

                expect($user->hasPermissionTo('customer_advisor.view-any'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.create'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.*.delete'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.*.force-delete'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.*.restore'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.*.update'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor.*.view'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor_embed.view-any'))->toBeTrue();
                expect($user->hasPermissionTo('customer_advisor_embed.*.view'))->toBeTrue();

                $settings->refresh();

                expect($settings->preselected_model)->toBe(AiModel::Test);
                expect($settings->instructions)->toBe('Test instructions');
                expect($settings->background_information)->toBe('Test background information');
                expect($settings->restrictions)->toBe('Test restrictions');

                expect(DB::table('permission_groups')->where('name', 'Customer Advisor')->exists())->toBeTrue();
                expect(DB::table('permission_groups')->where('name', 'Customer Advisor Embed')->exists())->toBeTrue();
                expect(DB::table('permission_groups')->where('name', 'QnA Advisor')->exists())->toBeFalse();
                expect(DB::table('permission_groups')->where('name', 'QnA Advisor Embed')->exists())->toBeFalse();

                expect(DB::table('settings')->where('group', 'ai-customer-advisor')->exists())->toBeTrue();
                expect(DB::table('settings')->where('group', 'ai-qna-advisor')->exists())->toBeFalse();
            }
        );
    });
});
