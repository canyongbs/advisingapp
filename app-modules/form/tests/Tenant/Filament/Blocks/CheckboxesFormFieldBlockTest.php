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

use AdvisingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;
use AdvisingApp\Form\Models\FormField;

it('validates the response against the option values, regardless of the stored options format', function (array $options) {
    $field = new FormField(['config' => ['options' => $options, 'hasOtherOption' => false]]);

    expect(CheckboxesFormFieldBlock::getValidationRules($field))->toBe([
        'array',
        'in:option-one,option-two',
    ]);
})->with([
    'legacy value => label map' => [
        ['option-one' => 'Option One', 'option-two' => 'Option Two'],
    ],
    'options repeater rows' => [
        [
            ['label' => 'Option One', 'value' => 'option-one'],
            ['label' => 'Option Two', 'value' => 'option-two'],
        ],
    ],
]);

it('allows any array response when the other option is enabled', function () {
    $field = new FormField(['config' => ['options' => ['option-one' => 'Option One'], 'hasOtherOption' => true]]);

    expect(CheckboxesFormFieldBlock::getValidationRules($field))->toBe(['array']);
});

describe('submission state', function () {
    it('marks the selected options as checked, regardless of the stored options format', function (array $options) {
        $field = new FormField(['config' => ['options' => $options]]);

        $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['option-one']);

        expect($state['response'])->toBe([
            'Option One' => true,
            'Option Two' => false,
        ]);
    })->with([
        'legacy value => label map' => [
            ['option-one' => 'Option One', 'option-two' => 'Option Two'],
        ],
        'options repeater rows' => [
            [
                ['label' => 'Option One', 'value' => 'option-one'],
                ['label' => 'Option Two', 'value' => 'option-two'],
            ],
        ],
    ]);

    it('matches option values case-insensitively', function () {
        $field = new FormField(['config' => ['options' => ['option-one' => 'Option One']]]);

        $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['OPTION-ONE']);

        expect($state['response'])->toBe(['Option One' => true]);
    });

    it('matches responses saved as the option label by their slug', function () {
        $field = new FormField(['config' => ['options' => ['united-states' => 'United States']]]);

        $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['United States']);

        expect($state['response'])->toBe(['United States' => true]);
    });

    it('still reports responses whose option no longer exists', function () {
        $field = new FormField(['config' => ['options' => ['united-states' => 'United States']]]);

        $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['us']);

        expect($state['response'])->toBe([
            'United States' => false,
            'us' => true,
        ]);
    });

    it('reports unmatched responses as other options when enabled', function () {
        $field = new FormField(['config' => [
            'options' => ['option-one' => 'Option One'],
            'hasOtherOption' => true,
        ]]);

        $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['option-one', 'Something else']);

        expect($state['response'])->toBe([
            'Option One' => true,
            'Other: Something else' => true,
        ]);
    });
});
