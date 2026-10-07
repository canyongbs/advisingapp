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

use AdvisingApp\Form\Filament\Blocks\SelectFormFieldBlock;
use AdvisingApp\Form\Filament\Forms\Components\OptionsKeyValue;
use AdvisingApp\Form\Models\FormField;
use Illuminate\Support\Facades\Validator;

it('renders a different preview when the options are only reordered', function (array $options) {
    $config = ['label' => 'Country', 'isRequired' => false];

    $original = SelectFormFieldBlock::toPreviewHtml([...$config, 'options' => $options]);
    $reordered = SelectFormFieldBlock::toPreviewHtml([...$config, 'options' => array_reverse($options)]);

    expect($reordered)->not->toBe($original);
})->with([
    'legacy map' => [['us' => 'United States', 'ca' => 'Canada']],
    'label and value rows' => [[
        ['label' => 'United States', 'value' => 'us'],
        ['label' => 'Canada', 'value' => 'ca'],
    ]],
]);

it('preserves generated numeric values through serialization and submission validation', function () {
    $component = collect(SelectFormFieldBlock::fields())->first(fn (mixed $field): bool => $field instanceof OptionsKeyValue);

    $options = [
        ['key' => '0', 'value' => '0%'],
        ['key' => '1', 'value' => '1%'],
    ];

    foreach ($component->getStateCasts() as $cast) {
        $options = $cast->get($options);
    }

    $field = new FormField();
    $field->label = 'Percentage';
    $field->is_required = true;
    $field->config = ['options' => json_decode(json_encode($options), associative: true)];

    $schema = SelectFormFieldBlock::getFormKitSchema($field);
    $rules = SelectFormFieldBlock::getValidationRules($field);

    expect($schema['options'])->toBe([
        ['label' => '0%', 'value' => '0'],
        ['label' => '1%', 'value' => '1'],
    ])->and(Validator::make(['response' => '0'], ['response' => $rules])->passes())->toBeTrue()
        ->and(Validator::make(['response' => '1'], ['response' => $rules])->passes())->toBeTrue()
        ->and(Validator::make(['response' => '0%'], ['response' => $rules])->passes())->toBeFalse();
});

it('renders and validates legacy option maps with their original values', function (array $options, array $expectedOptions, string $firstValue, string $secondValue, string $invalidValue) {
    $field = new FormField();
    $field->label = 'Percentage';
    $field->is_required = true;
    $field->config = ['options' => $options];

    $schema = SelectFormFieldBlock::getFormKitSchema($field);
    $rules = SelectFormFieldBlock::getValidationRules($field);

    expect(json_decode(json_encode($schema['options']), associative: true))->toBe($expectedOptions)
        ->and(Validator::make(['response' => $firstValue], ['response' => $rules])->passes())->toBeTrue()
        ->and(Validator::make(['response' => $secondValue], ['response' => $rules])->passes())->toBeTrue()
        ->and(Validator::make(['response' => $invalidValue], ['response' => $rules])->passes())->toBeFalse();
})->with([
    'numeric values including zero' => [
        ['0%', '1%'],
        [
            ['label' => '0%', 'value' => '0'],
            ['label' => '1%', 'value' => '1'],
        ],
        '0',
        '1',
        '0%',
    ],
    'custom values unrelated to their labels' => [
        ['LEGACY-ID-7' => 'Career planning', '01' => 'Academic advising'],
        [
            ['label' => 'Career planning', 'value' => 'LEGACY-ID-7'],
            ['label' => 'Academic advising', 'value' => '01'],
        ],
        'LEGACY-ID-7',
        '01',
        'Career planning',
    ],
    'nonsequential numeric values' => [
        [20 => '20%', 10 => '10%'],
        [
            ['label' => '20%', 'value' => '20'],
            ['label' => '10%', 'value' => '10'],
        ],
        '20',
        '10',
        '20%',
    ],
]);

it('displays the submitted option label', function (array $options, string $response, string $label) {
    $html = view('form::blocks.submissions.select', [
        'label' => 'Choice',
        'isRequired' => false,
        'options' => $options,
        'response' => $response,
    ])->render();

    expect($html)->toContain($label)->not->toContain('No response');
})->with([
    'legacy map' => [['us' => 'United States', 'ca' => 'Canada'], 'us', 'United States'],
    'legacy numeric map' => [['0%', '1%'], '0', '0%'],
    'legacy custom value' => [['LEGACY-ID-7' => 'Career planning'], 'LEGACY-ID-7', 'Career planning'],
    'legacy leading-zero value' => [['01' => 'Academic advising'], '01', 'Academic advising'],
    'numeric value rows' => [[
        ['label' => '0%', 'value' => '0'],
        ['label' => '1%', 'value' => '1'],
    ], '0', '0%'],
]);
