<?php

use AdvisingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\RadioFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use App\Features\FormRepeaterFeature;

it('converts radio and checkbox options to repeater format', function () {
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

    $migration = require database_path('migrations/2026_02_04_120050_tmp_data_convert_form_fields_options_to_repeater_format.php');
    $migration->up();

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
});

it('skips already converted fields', function () {
    $form = Form::factory()->create();

    $newOptions = [
        ['value' => 'option1', 'label' => 'Option 1 Label'],
    ];

    $radioField = FormField::factory()->create([
        'form_id' => $form->id,
        'type' => 'radio',
        'config' => [
            'options' => $newOptions,
        ],
    ]);

    $migration = require database_path('migrations/2026_02_04_120050_tmp_data_convert_form_fields_options_to_repeater_format.php');
    $migration->up();

    $radioField->refresh();
    expect($radioField->config['options'])->toBe($newOptions);
});
