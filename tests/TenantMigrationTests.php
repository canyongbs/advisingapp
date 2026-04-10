<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
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

/** @return array<string, mixed> */
function imageContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'attrs' => ['textAlign' => 'start'],
                'content' => [
                    [
                        'type' => 'image',
                        'attrs' => [
                            'id' => 'test-uuid',
                            'alt' => null,
                            'src' => null,
                            'title' => null,
                            'width' => 800,
                            'height' => 600,
                        ],
                    ],
                ],
            ],
            [
                'type' => 'paragraph',
                'attrs' => ['textAlign' => 'start'],
                'content' => [
                    [
                        'type' => 'image',
                        'attrs' => [
                            'id' => 'small-uuid',
                            'alt' => null,
                            'src' => null,
                            'title' => null,
                            'width' => 300,
                            'height' => 200,
                        ],
                    ],
                ],
            ],
        ],
    ];
}

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables email_templates', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $emailTemplate = EmailTemplate::factory()->createQuietly([
                'content' => imageContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('email_templates')->where('id', $emailTemplate->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables engagements', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $engagement = Engagement::factory()->createQuietly([
                'body' => imageContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('engagements')->where('id', $engagement->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables engagement_batches', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $batch = EngagementBatch::factory()->createQuietly([
                'body' => imageContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('engagement_batches')->where('id', $batch->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables workflow_engagement_email_details', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $details = WorkflowEngagementEmailDetails::factory()->createQuietly([
                'body' => imageContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('workflow_engagement_email_details')->where('id', $details->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables campaign_actions', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $action = CampaignAction::factory()->createQuietly([
                'data' => [
                    'channel' => 'email',
                    'subject' => [],
                    'body' => imageContent(),
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $data = json_decode((string) DB::table('campaign_actions')->where('id', $action->id)->value('data'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

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

/** @return array<string, mixed> */
function textColorContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'attrs' => ['textAlign' => 'start'],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello world',
                        'marks' => [
                            [
                                'type' => 'textStyle',
                                'attrs' => [
                                    'color' => '#ff0000',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'paragraph',
                'attrs' => ['textAlign' => 'start'],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'No color',
                        'marks' => [
                            [
                                'type' => 'bold',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables transforms textStyle marks to textColor', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $emailTemplate = EmailTemplate::factory()->createQuietly([
                'content' => textColorContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('email_templates')->where('id', $emailTemplate->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            // textStyle mark should be transformed to textColor with data-color attr
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['type'])->toBe('textColor');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['attrs']['data-color'])->toBe('#ff0000');

            // bold mark should be unchanged
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['marks'][0]['type'])->toBe('bold');
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables transforms textStyle marks in engagements', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $engagement = Engagement::factory()->createQuietly([
                'body' => textColorContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('engagements')->where('id', $engagement->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['marks'][0]['type'])->toBe('textColor');
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][0]['marks'][0]['attrs']['data-color'])->toBe('#ff0000');
        }
    );
});

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables transforms textStyle marks in campaign_actions', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $action = CampaignAction::factory()->createQuietly([
                'data' => [
                    'channel' => 'email',
                    'subject' => [],
                    'body' => textColorContent(),
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $data = json_decode((string) DB::table('campaign_actions')->where('id', $action->id)->value('data'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][0]['content'][0]['marks'][0]['type'])->toBe('textColor');
            /** @phpstan-ignore-next-line */
            expect($data['body']['content'][0]['content'][0]['marks'][0]['attrs']['data-color'])->toBe('#ff0000');
        }
    );
});

/** @return array<string, mixed> */
function studentMergeTagContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'student first name']],
                    ['type' => 'text', 'text' => ' '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'student last name']],
                ],
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'student email']],
                    ['type' => 'text', 'text' => ' - '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient first name']],
                ],
            ],
        ],
    ];
}

test('2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables renames student merge tags to recipient', function () {
    isolatedMigration(
        '2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables',
        function () {
            $engagement = Engagement::factory()->createQuietly([
                'body' => studentMergeTagContent(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_03_24_192248_tmp_data_process_rich_content_in_engagement_tables.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('engagements')->where('id', $engagement->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            // student merge tags should be renamed to recipient
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][1]['attrs']['id'])->toBe('recipient first name');
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['content'][3]['attrs']['id'])->toBe('recipient last name');
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][0]['attrs']['id'])->toBe('recipient email');

            // existing recipient merge tags should be unchanged
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['content'][2]['attrs']['id'])->toBe('recipient first name');
        }
    );
});
