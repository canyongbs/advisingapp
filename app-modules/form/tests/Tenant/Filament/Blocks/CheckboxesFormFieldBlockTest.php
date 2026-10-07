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
use AdvisingApp\Form\Filament\Forms\Components\OptionsKeyValue;
use AdvisingApp\Form\Models\FormField;

it('saves numeric option values as explicit label and value rows', function () {
    $field = collect(CheckboxesFormFieldBlock::fields())->first(fn (mixed $field): bool => $field instanceof OptionsKeyValue);

    $state = [
        ['key' => '0', 'value' => 'No'],
        ['key' => '1', 'value' => 'Yes'],
    ];

    foreach ($field->getStateCasts() as $cast) {
        $state = $cast->get($state);
    }

    expect($state)->toBe([
        ['label' => 'No', 'value' => '0'],
        ['label' => 'Yes', 'value' => '1'],
    ]);
});

it('hydrates saved numeric option rows into the editor without losing their values', function () {
    $field = collect(CheckboxesFormFieldBlock::fields())->first(fn (mixed $field): bool => $field instanceof OptionsKeyValue);

    $state = [
        ['label' => 'No', 'value' => '0'],
        ['label' => 'Yes', 'value' => '1'],
    ];

    foreach (array_reverse($field->getStateCasts()) as $cast) {
        $state = $cast->set($state);
    }

    expect($state)->toBe([
        ['key' => '0', 'value' => 'No'],
        ['key' => '1', 'value' => 'Yes'],
    ]);
});

it('renders the labels of numeric options in the preview', function () {
    $html = CheckboxesFormFieldBlock::toPreviewHtml([
        'label' => 'Agree',
        'options' => [
            ['label' => 'No', 'value' => '0'],
            ['label' => 'Yes', 'value' => '1'],
        ],
    ]);

    expect($html)->toContain('No')->toContain('Yes');
});

it('resolves numeric responses to their labels in the submission state', function () {
    $field = new FormField();
    $field->forceFill([
        'config' => [
            'options' => [
                ['label' => 'No', 'value' => '0'],
                ['label' => 'Yes', 'value' => '1'],
            ],
        ],
    ]);

    $state = CheckboxesFormFieldBlock::getSubmissionState($field, ['1']);

    expect($state['response'])->toBe(['No' => false, 'Yes' => true]);
});
