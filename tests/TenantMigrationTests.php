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

use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\CaseManagement\Models\CaseTypeEmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\MeetingCenter\Models\Event as MeetingCenterEvent;
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

/** @return array<string, mixed> */
function oldTiptapCaseFormContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'case-field-uuid-1',
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
                    'id' => 'case-field-uuid-2',
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
function oldTiptapCaseEmailTemplateBody(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'contact name']],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => null,
                    'type' => 'caseTypeEmailTemplateButtonBlock',
                    'data' => [
                        'label' => 'Open Case',
                        'alignment' => 'center',
                    ],
                ],
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function oldTiptapEventRegistrationFormContent(): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Register below'],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'event-field-uuid-1',
                    'type' => 'educatable_name',
                    'data' => [
                        'label' => 'Name',
                        'isRequired' => true,
                        'firstNameLabel' => 'First Name',
                    ],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'event-field-uuid-2',
                    'type' => 'select',
                    'data' => [
                        'label' => 'Session',
                        'isRequired' => false,
                        'options' => ['am' => 'Morning', 'pm' => 'Afternoon'],
                    ],
                ],
            ],
        ],
    ];
}

$caseManagementMigrationPath = 'app-modules/case-management/database/migrations/2026_06_18_234750_tmp_migrate_from_content_tiptap_to_richeditor_for_case_management.php';

test('2026_06_18_234750 converts tiptapBlock to customBlock in case_forms', function () use ($caseManagementMigrationPath) {
    isolatedMigration(
        '2026_06_18_234750_tmp_migrate_from_content_tiptap_to_richeditor_for_case_management',
        function () use ($caseManagementMigrationPath) {
            $caseFormId = (string) Str::uuid();

            DB::table('case_forms')->insert([
                'id' => $caseFormId,
                'name' => 'Test Case Form ' . Str::uuid(),
                'content' => json_encode(oldTiptapCaseFormContent()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => $caseManagementMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('case_forms')->where('id', $caseFormId)->value('content'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['id'])->toBe('text_input');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['fieldId'])->toBe('case-field-uuid-1');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['label'])->toBe('Full Name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['isRequired'])->toBeTrue();

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['id'])->toBe('select');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['fieldId'])->toBe('case-field-uuid-2');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['options'])->toBe(['red' => 'Red', 'blue' => 'Blue']);

            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['type'])->toBe('paragraph');
        }
    );
});

test('2026_06_18_234750 converts caseTypeEmailTemplateButtonBlock in case_type_email_templates body', function () use ($caseManagementMigrationPath) {
    isolatedMigration(
        '2026_06_18_234750_tmp_migrate_from_content_tiptap_to_richeditor_for_case_management',
        function () use ($caseManagementMigrationPath) {
            $template = CaseTypeEmailTemplate::factory()->createQuietly();

            DB::table('case_type_email_templates')
                ->where('id', $template->id)
                ->update(['body' => json_encode(oldTiptapCaseEmailTemplateBody())]);

            $migrate = Artisan::call('migrate', ['--path' => $caseManagementMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $body = json_decode((string) DB::table('case_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            // Paragraph node is unchanged
            /** @phpstan-ignore-next-line */
            expect($body['content'][0]['type'])->toBe('paragraph');

            // Button block is converted
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['attrs']['id'])->toBe('caseTypeEmailTemplateButtonBlock');
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['attrs']['config']['label'])->toBe('Open Case');
            /** @phpstan-ignore-next-line */
            expect($body['content'][1]['attrs']['config']['alignment'])->toBe('center');
        }
    );
});

$meetingCenterMigrationPath = 'app-modules/meeting-center/database/migrations/2026_06_18_153618_tmp_migrate_from_content_tiptap_to_richeditor_for_meeting_center.php';

test('2026_06_18_153618 converts tiptapBlock to customBlock in event_registration_forms', function () use ($meetingCenterMigrationPath) {
    isolatedMigration(
        '2026_06_18_153618_tmp_migrate_from_content_tiptap_to_richeditor_for_meeting_center',
        function () use ($meetingCenterMigrationPath) {
            $event = MeetingCenterEvent::factory()->createQuietly();

            $formId = (string) Str::uuid();

            DB::table('event_registration_forms')->insert([
                'id' => $formId,
                'event_id' => $event->getKey(),
                'embed_enabled' => false,
                'is_wizard' => false,
                'recaptcha_enabled' => false,
                'content' => json_encode(oldTiptapEventRegistrationFormContent()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $migrate = Artisan::call('migrate', ['--path' => $meetingCenterMigrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('event_registration_forms')->where('id', $formId)->value('content'), associative: true); /** @phpstan-ignore-line */

            // Paragraph node is unchanged
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('paragraph');

            // educatable_name block is converted
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['id'])->toBe('educatable_name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['fieldId'])->toBe('event-field-uuid-1');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['label'])->toBe('Name');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['isRequired'])->toBeTrue();

            // select block is converted
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['attrs']['id'])->toBe('select');
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['attrs']['config']['fieldId'])->toBe('event-field-uuid-2');
            /** @phpstan-ignore-next-line */
            expect($content['content'][2]['attrs']['config']['options'])->toBe(['am' => 'Morning', 'pm' => 'Afternoon']);
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

describe('2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor', function () {
    $migrationPath = 'app-modules/ai/database/migrations/2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor.php';

    it('converts legacy qna_advisor morph types in audits table', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $advisor = CustomerAdvisor::factory()->createQuietly();

                $auditId = (string) Str::uuid();
                DB::table('audits')->insert([
                    'id' => $auditId,
                    'event' => 'created',
                    'auditable_type' => 'qna_advisor',
                    'auditable_id' => $advisor->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $audit = DB::table('audits')->where('id', $auditId)->first();
                expect($audit->auditable_type)->toBe('customer_advisor');
            }
        );
    });

    it('converts legacy qna_advisor_category morph types in audits table', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $category = CustomerAdvisorCategory::factory()->createQuietly();

                $auditId = (string) Str::uuid();
                DB::table('audits')->insert([
                    'id' => $auditId,
                    'event' => 'created',
                    'auditable_type' => 'qna_advisor_category',
                    'auditable_id' => $category->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $audit = DB::table('audits')->where('id', $auditId)->first();
                expect($audit->auditable_type)->toBe('customer_advisor_category');
            }
        );
    });

    it('converts legacy qna_advisor morph types in media table', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $advisor = CustomerAdvisor::factory()->createQuietly();

                DB::table('media')->insert([
                    'model_type' => 'qna_advisor',
                    'model_id' => $advisor->id,
                    'collection_name' => 'avatar',
                    'name' => 'test',
                    'file_name' => 'test.png',
                    'disk' => 'local',
                    'size' => 1024,
                    'manipulations' => '[]',
                    'custom_properties' => '[]',
                    'generated_conversions' => '[]',
                    'responsive_images' => '[]',
                ]);

                $mediaId = DB::table('media')->where('model_id', $advisor->id)->value('id');

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $media = DB::table('media')->where('id', $mediaId)->first();
                expect($media->model_type)->toBe('customer_advisor');
            }
        );
    });

    it('converts legacy qna_advisor_file morph types in open_ai_vector_stores table', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $file = CustomerAdvisorFile::factory()->createQuietly();

                $vectorStoreId = (string) Str::uuid();
                DB::table('open_ai_vector_stores')->insert([
                    'id' => $vectorStoreId,
                    'file_type' => 'qna_advisor_file',
                    'file_id' => $file->id,
                    'deployment_hash' => 'test-hash',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $vectorStore = DB::table('open_ai_vector_stores')->where('id', $vectorStoreId)->first();
                expect($vectorStore->file_type)->toBe('customer_advisor_file');
            }
        );
    });

    it('converts legacy qna_advisor morph types in open_ai_vector_stores context column', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $advisor = CustomerAdvisor::factory()->createQuietly();
                $file = CustomerAdvisorFile::factory()->createQuietly();

                $vectorStoreId = (string) Str::uuid();
                DB::table('open_ai_vector_stores')->insert([
                    'id' => $vectorStoreId,
                    'file_type' => 'customer_advisor_file',
                    'file_id' => $file->id,
                    'context_type' => 'qna_advisor',
                    'context_id' => $advisor->id,
                    'deployment_hash' => 'test-hash',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $vectorStore = DB::table('open_ai_vector_stores')->where('id', $vectorStoreId)->first();
                expect($vectorStore->context_type)->toBe('customer_advisor');
            }
        );
    });

    it('does not modify rows that already have customer_advisor morph types', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $advisor = CustomerAdvisor::factory()->createQuietly();

                $auditId = (string) Str::uuid();
                DB::table('audits')->insert([
                    'id' => $auditId,
                    'event' => 'created',
                    'auditable_type' => 'customer_advisor',
                    'auditable_id' => $advisor->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                $audit = DB::table('audits')->where('id', $auditId)->first();
                expect($audit->auditable_type)->toBe('customer_advisor');
            }
        );
    });

    it('can be rolled back', function () use ($migrationPath) {
        isolatedMigration(
            '2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor',
            function () use ($migrationPath) {
                $advisor = CustomerAdvisor::factory()->createQuietly();

                $auditId = (string) Str::uuid();
                DB::table('audits')->insert([
                    'id' => $auditId,
                    'event' => 'created',
                    'auditable_type' => 'qna_advisor',
                    'auditable_id' => $advisor->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Artisan::call('migrate', ['--path' => $migrationPath]);

                $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);

                expect($rollback)->toBe(Command::SUCCESS);

                $audit = DB::table('audits')->where('id', $auditId)->first();
                expect($audit->auditable_type)->toBe('qna_advisor');
            }
        );
    });
});
