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
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
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

$fixTiptapMigrationPath = 'app-modules/form/database/migrations/2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks.php';

/** @return array<string, mixed> */
function contentWithObjectKeys(): array
{
    return [
        'type' => 'doc',
        'content' => [
            0 => [
                'type' => 'customBlock',
                'attrs' => [
                    'config' => ['label' => 'First Name', 'isRequired' => true, 'fieldId' => 'field-1'],
                    'id' => 'text_input',
                ],
            ],
            1 => [
                'type' => 'customBlock',
                'attrs' => [
                    'config' => ['label' => 'Last Name', 'isRequired' => true, 'fieldId' => 'field-2'],
                    'id' => 'text_input',
                ],
            ],
            4 => [
                'type' => 'customBlock',
                'attrs' => [
                    'config' => ['label' => 'Email', 'isRequired' => true, 'fieldId' => 'field-3'],
                    'id' => 'text_input',
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function contentWithTextStyleMarks(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'heading',
                'attrs' => ['level' => 1],
                'content' => [
                    [
                        'type' => 'text',
                        'marks' => [
                            ['type' => 'textStyle', 'attrs' => ['style' => 'font-size:23pt;color:#999999;']],
                            ['type' => 'bold'],
                        ],
                        'text' => 'Hello World',
                    ],
                ],
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'marks' => [
                            ['type' => 'textStyle', 'attrs' => ['style' => 'font-size:14pt;']],
                        ],
                        'text' => 'Some paragraph text',
                    ],
                ],
            ],
            [
                'type' => 'customBlock',
                'attrs' => [
                    'config' => ['label' => 'Name', 'isRequired' => true, 'fieldId' => 'field-1'],
                    'id' => 'text_input',
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function contentWithBothIssues(): array
{
    return [
        'type' => 'doc',
        'content' => [
            0 => [
                'type' => 'heading',
                'attrs' => ['level' => 1],
                'content' => [
                    [
                        'type' => 'text',
                        'marks' => [
                            ['type' => 'textStyle', 'attrs' => ['style' => 'font-size:23pt;']],
                            ['type' => 'bold'],
                        ],
                        'text' => 'Title',
                    ],
                ],
            ],
            3 => [
                'type' => 'customBlock',
                'attrs' => [
                    'config' => ['label' => 'Field', 'isRequired' => true, 'fieldId' => 'field-1'],
                    'id' => 'text_input',
                ],
            ],
        ],
    ];
}

test('migration fixes content stored as object with numeric keys in forms', function () use ($fixTiptapMigrationPath) {
    isolatedMigration(
        '2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks',
        function () use ($fixTiptapMigrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);

            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(contentWithObjectKeys())]);

            $raw = DB::table('forms')->where('id', $form->id)->value('content');
            expect($raw)->toContain('"0":'); /** @phpstan-ignore-line */
            $migrate = Artisan::call('migrate', ['--path' => $fixTiptapMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect(array_is_list($content['content']))->toBeTrue();
            /** @phpstan-ignore-next-line */
            expect($content['content'])->toHaveCount(3);
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['label'])->toBe('First Name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['label'])->toBe('Last Name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['attrs']['config']['label'])->toBe('Email');
        }
    );
});

test('migration strips textStyle marks from form content', function () use ($fixTiptapMigrationPath) {
    isolatedMigration(
        '2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks',
        function () use ($fixTiptapMigrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);

            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(contentWithTextStyleMarks())]);

            $migrate = Artisan::call('migrate', ['--path' => $fixTiptapMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            $headingText = $content['content'][0]['content'][0];
            expect($headingText['marks'])->toHaveCount(1);
            expect($headingText['marks'][0]['type'])->toBe('bold');

            /** @phpstan-ignore-next-line */
            $paragraphText = $content['content'][1]['content'][0];
            expect($paragraphText)->not->toHaveKey('marks');
            expect($paragraphText['text'])->toBe('Some paragraph text');

            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['type'])->toBe('customBlock');
        }
    );
});

test('migration fixes both object keys and textStyle marks together', function () use ($fixTiptapMigrationPath) {
    isolatedMigration(
        '2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks',
        function () use ($fixTiptapMigrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);

            DB::table('forms')->where('id', $form->id)->update(['content' => json_encode(contentWithBothIssues())]);

            $migrate = Artisan::call('migrate', ['--path' => $fixTiptapMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('forms')->where('id', $form->id)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect(array_is_list($content['content']))->toBeTrue();
            /** @phpstan-ignore-next-line */
            expect($content['content'])->toHaveCount(2);

            /** @phpstan-ignore-next-line */
            $headingText = $content['content'][0]['content'][0];
            expect($headingText['marks'])->toHaveCount(1);
            expect($headingText['marks'][0]['type'])->toBe('bold');

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('customBlock');
        }
    );
});

test('migration does not modify content that has no issues', function () use ($fixTiptapMigrationPath) {
    isolatedMigration(
        '2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks',
        function () use ($fixTiptapMigrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);

            $cleanContent = [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'heading',
                        'attrs' => ['level' => 1],
                        'content' => [
                            ['type' => 'text', 'marks' => [['type' => 'bold']], 'text' => 'Title'],
                        ],
                    ],
                    [
                        'type' => 'customBlock',
                        'attrs' => [
                            'config' => ['label' => 'Name', 'isRequired' => true, 'fieldId' => 'field-1'],
                            'id' => 'text_input',
                        ],
                    ],
                ],
            ];

            $encoded = json_encode($cleanContent);
            DB::table('forms')->where('id', $form->id)->update(['content' => $encoded]);

            $migrate = Artisan::call('migrate', ['--path' => $fixTiptapMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $raw = DB::table('forms')->where('id', $form->id)->value('content');
            expect($raw)->toBe($encoded);
        }
    );
});

test('migration skips null content in forms', function () use ($fixTiptapMigrationPath) {
    isolatedMigration(
        '2026_04_30_095742_fix_tiptap_content_array_keys_and_text_style_marks',
        function () use ($fixTiptapMigrationPath) {
            $form = Form::factory()->createQuietly();
            FormField::factory()->createQuietly(['form_id' => $form->id]);
            DB::table('forms')->where('id', $form->id)->update(['content' => null]);

            $migrate = Artisan::call('migrate', ['--path' => $fixTiptapMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = DB::table('forms')->where('id', $form->id)->value('content');
            expect($content)->toBeNull();
        }
    );
});
