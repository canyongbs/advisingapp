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

use AdvisingApp\Form\Enums\ProspectGenerationField;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
use AdvisingApp\Form\Models\Form;

$emailDocument = fn (): array => ['type' => 'doc', 'content' => [
    ['type' => 'customBlock', 'attrs' => ['id' => EducatableEmailFormFieldBlock::type(), 'config' => ['isRequired' => true]]],
]];

$nameDocument = fn (): array => ['type' => 'doc', 'content' => [
    ['type' => 'customBlock', 'attrs' => ['id' => EducatableNameFormFieldBlock::type(), 'config' => []]],
]];

describe('getMissingRequiredProspectGenerationFields', function () use ($emailDocument, $nameDocument): void {
    it('reports the fields missing from a single page form that generates prospects', function () use ($emailDocument): void {
        $form = Form::factory()->create(['generate_prospects' => true, 'is_authenticated' => false]);
        $form->update(['content' => $emailDocument()]);

        expect($form->getMissingRequiredProspectGenerationFields())->toBe([ProspectGenerationField::Name])
            ->and($form->isMissingRequiredProspectGenerationFields())->toBeTrue();
    });

    it('checks the steps of a multi-step form rather than its content', function () use ($emailDocument, $nameDocument): void {
        $form = Form::factory()->create(['generate_prospects' => true, 'is_authenticated' => false, 'is_wizard' => true]);
        $form->update(['content' => null]);
        $form->steps()->create(['label' => 'First', 'content' => $emailDocument(), 'sort' => 0]);
        $form->steps()->create(['label' => 'Second', 'content' => $nameDocument(), 'sort' => 1]);

        expect($form->refresh()->getMissingRequiredProspectGenerationFields())->toBe([])
            ->and($form->isMissingRequiredProspectGenerationFields())->toBeFalse();
    });

    it('reports nothing missing for a form that does not generate prospects without authentication', function (array $attributes): void {
        $form = Form::factory()->create($attributes);

        expect($form->getMissingRequiredProspectGenerationFields())->toBe([]);
    })->with([
        'does not generate prospects' => [['generate_prospects' => false, 'is_authenticated' => false]],
        'requires authentication' => [['generate_prospects' => true, 'is_authenticated' => true]],
    ]);
});
