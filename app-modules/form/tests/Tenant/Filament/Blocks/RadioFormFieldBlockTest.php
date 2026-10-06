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

use AdvisingApp\Form\Filament\Blocks\RadioFormFieldBlock;
use AdvisingApp\Form\Models\FormField;

it('validates the response against the option values, regardless of the stored options format', function (array $options) {
    $field = new FormField(['config' => ['options' => $options, 'hasOtherOption' => false]]);

    expect(RadioFormFieldBlock::getValidationRules($field))->toBe([
        'string',
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

it('shows the option label for a submitted value, falling back to the submitted value when its option no longer exists', function (string $response, string $expected) {
    $html = RadioFormFieldBlock::toHtml([
        'label' => 'Country',
        'isRequired' => false,
        'options' => ['united-states' => 'United States'],
        'response' => $response,
    ], []);

    expect($html)->toContain($expected);
})->with([
    'current option' => ['united-states', 'United States'],
    'value saved as the label' => ['United States', 'United States'],
    'removed option' => ['us', 'us'],
]);

it('allows any string response when the other option is enabled', function () {
    $field = new FormField(['config' => ['options' => ['option-one' => 'Option One'], 'hasOtherOption' => true]]);

    expect(RadioFormFieldBlock::getValidationRules($field))->toBe(['string']);
});
