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

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveyField;
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



/** @return array<string, mixed> */
function oldTiptapFormContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'field-uuid-1',
                    'type' => 'text_input',
                    'data' => [
                        'label' => 'Full Name',
                        'isRequired' => true,
                        'description' => 'Enter your name',
                    ],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'field-uuid-2',
                    'type' => 'select',
                    'data' => [
                        'label' => 'Color',
                        'isRequired' => false,
                        'options' => ['red' => 'Red', 'blue' => 'Blue'],
                    ],
                ],
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Some text'],
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function oldTiptapGridContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'grid',
                'attrs' => ['type' => 'responsive', 'cols' => '2'],
                'content' => [
                    [
                        'type' => 'gridColumn',
                        'content' => [
                            [
                                'type' => 'tiptapBlock',
                                'attrs' => [
                                    'id' => 'grid-field-1',
                                    'type' => 'text_input',
                                    'data' => ['label' => 'Left Field', 'isRequired' => false],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'gridColumn',
                        'content' => [
                            [
                                'type' => 'tiptapBlock',
                                'attrs' => [
                                    'id' => 'grid-field-2',
                                    'type' => 'text_input',
                                    'data' => ['label' => 'Right Field', 'isRequired' => true],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function oldTiptapAsymmetricGridContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'grid',
                'attrs' => ['type' => 'asymetric-left-thirds', 'cols' => '3'],
                'content' => [
                    [
                        'type' => 'gridColumn',
                        'content' => [
                            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Narrow']]],
                        ],
                    ],
                    [
                        'type' => 'gridColumn',
                        'content' => [
                            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Wide']]],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function oldTiptapImageContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'id' => 'img-uuid',
                    'src' => null,
                    'width' => 800,
                    'height' => 600,
                ],
            ],
            [
                'type' => 'image',
                'attrs' => [
                    'id' => 'small-img-uuid',
                    'src' => null,
                    'width' => 300,
                    'height' => 200,
                ],
            ],
        ],
    ];
}

$migrationPath = 'app-modules/form/database/migrations/2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications.php';

test('migration converts tiptapBlock to customBlock in forms', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $form = Form::factory()->createQuietly();

            // Create a dummy field so factory afterCreating doesn't overwrite content
            FormField::factory()->createQuietly(['form_id' => $form->id]);

            // Set old tiptap content directly via DB
            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(oldTiptapFormContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            // First block: text_input
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['id'])->toBe('text_input');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['fieldId'])->toBe('field-uuid-1');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['label'])->toBe('Full Name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['isRequired'])->toBeTrue();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['description'])->toBe('Enter your name');

            // Second block: select
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['id'])->toBe('select');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['fieldId'])->toBe('field-uuid-2');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['options'])->toBe(['red' => 'Red', 'blue' => 'Blue']);

            // Third: paragraph unchanged
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['type'])->toBe('paragraph');
        }
    );
});

test('migration converts responsive grid in forms', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);
            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(oldTiptapGridContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            $grid = $content['content'][0];
            expect($grid['type'])->toBe('grid');
            expect($grid['attrs']['data-cols'])->toBe('2');
            expect($grid['attrs']['data-from-breakpoint'])->toBe('lg');

            // Grid columns have data-col-span
            expect($grid['content'][0]['attrs']['data-col-span'])->toBe('1');
            expect($grid['content'][1]['attrs']['data-col-span'])->toBe('1');

            // Blocks inside grid are converted
            expect($grid['content'][0]['content'][0]['type'])->toBe('customBlock');
            expect($grid['content'][0]['content'][0]['attrs']['id'])->toBe('text_input');
            expect($grid['content'][0]['content'][0]['attrs']['config']['fieldId'])->toBe('grid-field-1');
        }
    );
});

test('migration converts asymmetric grid in forms', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);
            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(oldTiptapAsymmetricGridContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            $grid = $content['content'][0];
            expect($grid['attrs']['data-cols'])->toBe('3');
            expect($grid['attrs']['data-from-breakpoint'])->toBe('lg');

            // Asymmetric left thirds: first col span 1, second col span 2
            expect($grid['content'][0]['attrs']['data-col-span'])->toBe('1');
            expect($grid['content'][1]['attrs']['data-col-span'])->toBe('2');
        }
    );
});

test('migration fixes oversized images in forms', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);
            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(oldTiptapImageContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            // Oversized image: dimensions cleared
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['height'])->toBeNull();

            // Small image: dimensions preserved
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['height'])->toBe(200);
        }
    );
});

test('migration converts tiptapBlock to customBlock in surveys', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $survey = Survey::factory()->createQuietly();
            SurveyField::factory()->createQuietly(['survey_id' => $survey->id]);
            DB::table('surveys')->where('id', $survey->id)->update(['content' => json_encode(oldTiptapFormContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('surveys')->where('id', $survey->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['id'])->toBe('text_input');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['fieldId'])->toBe('field-uuid-1');
        }
    );
});

test('migration converts tiptapBlock to customBlock in applications', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $application = Application::factory()->makeOne();
            $application->saveQuietly();
            DB::table('applications')->where('id', $application->id)->update(['content' => json_encode(oldTiptapFormContent())]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('applications')->where('id', $application->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['id'])->toBe('text_input');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['fieldId'])->toBe('field-uuid-1');
        }
    );
});

test('migration skips null content', function () use ($migrationPath) {
    isolatedMigration(
        '2026_04_01_205852_tmp_migrate_from_content_tiptap_to_richeditor_for_forms_survey_and_applications',
        function () use ($migrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);
            DB::table('forms')->where('id', $form->id)->update(['content' => null]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = DB::table('forms')->where('id', $form->id)->value('content');
            expect($content)->toBeNull();
        }
    );
});

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
