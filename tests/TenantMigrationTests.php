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

// Add tests for migration files here

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;
use AdvisingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\RadioFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use App\Features\FormRepeaterFeature;

describe('2026_02_10_161725_tmp_data_remove_sms_opt_out_and_email_bounce_filters_from_segments', function () {
    it('removes sms_opt_out and email_bounce filter types from groups', function () {
        isolatedMigration(
            '2026_02_10_161725_tmp_data_remove_sms_opt_out_and_email_bounce_filters_from_segments',
            function () {
                $user = User::factory()->create();

                $groupWithMixed = Group::factory()->create([
                    'model' => GroupModel::Student,
                    'user_id' => $user->id,
                    'filters' => [
                        'queryBuilder' => [
                            'rules' => [
                                'obs1' => ['type' => 'sms_opt_out', 'data' => ['operator' => 'isTrue', 'settings' => null]],
                                'valid1' => ['type' => 'dual', 'data' => ['operator' => 'isTrue', 'settings' => null]],
                            ],
                        ],
                    ],
                ]);

                $groupWithOr = Group::factory()->create([
                    'model' => GroupModel::Student,
                    'user_id' => $user->id,
                    'filters' => [
                        'queryBuilder' => [
                            'rules' => [
                                'or1' => [
                                    'type' => 'or',
                                    'data' => [
                                        'groups' => [
                                            'grp1' => [
                                                'rules' => [
                                                    'obs2' => ['type' => 'email_bounce', 'data' => ['operator' => 'isTrue']],
                                                    'valid2' => ['type' => 'holds', 'data' => ['operator' => 'contains', 'settings' => ['text' => 'test']]],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);

                $groupWithNull = Group::factory()->create([
                    'model' => GroupModel::Student,
                    'user_id' => $user->id,
                    'filters' => null,
                ]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/group/database/migrations/2026_02_10_161725_tmp_data_remove_sms_opt_out_and_email_bounce_filters_from_segments.php']);
                expect($migrate)->toBe(Command::SUCCESS);

                $groupWithMixed->refresh();
                $groupWithOr->refresh();
                $groupWithNull->refresh();

                expect($groupWithMixed->filters['queryBuilder']['rules'])->toHaveKey('valid1');
                expect($groupWithMixed->filters['queryBuilder']['rules'])->not->toHaveKey('obs1');

                expect($groupWithOr->filters['queryBuilder']['rules']['or1']['data']['groups']['grp1']['rules'])->toHaveKey('valid2');
                expect($groupWithOr->filters['queryBuilder']['rules']['or1']['data']['groups']['grp1']['rules'])->not->toHaveKey('obs2');

                expect($groupWithNull->filters)->toBeNull();
            }
        );
    });
});

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
describe('2026_02_04_120050_tmp_data_convert_form_fields_options_to_repeater_format', function () {
    it('converts radio and checkbox options to repeater format', function () {
        isolatedMigration(
            '2026_02_04_120050_tmp_data_convert_form_fields_options_to_repeater_format',
            function () {
                $form = Form::factory()->create();

                $radioField = FormField::factory()->create([
                    'form_id' => $form->id,
                    'type' => 'radio',
                    'config' => [
                        'options' => [
                            'option1' => 'Option 1 Label',
                            'option2' => 'Option 2 Label',
                        ],
                    ],
                ]);

                $checkboxesField = FormField::factory()->create([
                    'form_id' => $form->id,
                    'type' => 'checkboxes',
                    'config' => [
                        'options' => [
                            'check1' => 'Check 1 Label',
                            'check2' => 'Check 2 Label',
                        ],
                    ],
                ]);

                $selectField = FormField::factory()->create([
                    'form_id' => $form->id,
                    'type' => 'select',
                    'config' => ['description' => 'desc', 'options' => ['1' => '1', '2' => '2', '3' => '3']],
                ]);

                $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_02_04_120050_tmp_data_convert_form_fields_options_to_repeater_format.php']);
                expect($migrate)->toBe(Command::SUCCESS);

                $radioField->refresh();
                $checkboxesField->refresh();
                $selectField->refresh();

                expect($radioField->config['options'])->toBe([
                    ['value' => 'option1', 'label' => 'Option 1 Label'],
                    ['value' => 'option2', 'label' => 'Option 2 Label'],
                ]);

                expect($checkboxesField->config['options'])->toBe([
                    ['value' => 'check1', 'label' => 'Check 1 Label'],
                    ['value' => 'check2', 'label' => 'Check 2 Label'],
                ]);

                expect($selectField->config)->toBe(['description' => 'desc', 'options' => ['1' => '1', '2' => '2', '3' => '3']]);

                FormRepeaterFeature::activate();

                try {
                    $validationRules = RadioFormFieldBlock::getValidationRules($radioField);
                    expect($validationRules)->toBeArray();

                    $schema = RadioFormFieldBlock::getFormKitSchema($radioField);
                    expect($schema)->toBeArray()
                        ->toHaveKey('options');

                    expect($schema['options'])->toBeArray()
                        ->toBe($radioField->config['options']);
                } catch (Throwable $exception) {
                    $this->fail('RadioFormFieldBlock threw exception with converted data: ' . $exception->getMessage());
                }

                try {
                    $validationRules = CheckboxesFormFieldBlock::getValidationRules($checkboxesField);
                    expect($validationRules)->toBeArray();

                    $schema = CheckboxesFormFieldBlock::getFormKitSchema($checkboxesField);
                    expect($schema)->toBeArray();

                    expect($schema['options'])->toBe($checkboxesField->config['options']);
                } catch (Throwable $exception) {
                    $this->fail('CheckboxesFormFieldBlock threw exception with converted data: ' . $exception->getMessage());
                }
            }
        );
    });
});