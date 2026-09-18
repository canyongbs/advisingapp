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

use AdvisingApp\MeetingCenter\Actions\GenerateEventRegistrationFormKitSchema;
use AdvisingApp\MeetingCenter\Models\Event;

it('gates the step progress bar behind totalSteps and an affirmative attending answer', function () {
    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $form->steps()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'sort' => 0],
        ['label' => 'Dietary Preferences', 'sort' => 1],
    ]);

    $schema = app(GenerateEventRegistrationFormKitSchema::class)($form->refresh());
    $encodedSchema = json_encode($schema, JSON_THROW_ON_ERROR);

    $stepLoader = $schema['children'][1];

    expect($schema['children'][0]['$formkit'])->toBe('radio')
        ->and($schema['children'][0]['id'])->toBe('attending')
        ->and($stepLoader['attrs']['class'])->toBe('step-loader not-prose')
        ->and($stepLoader['if'])->toBe('$totalSteps > 1 && $get(attending).value === "yes"')
        ->and($encodedSchema)
        ->toContain('step-loader__label')
        ->toContain('$currentStep + \\" of \\" + $totalSteps')
        ->toContain('step-loader__percent')
        ->toContain('$percentComplete + \\"% complete\\"')
        ->toContain('step-loader__fill')
        ->toContain('width: ${$percentComplete}%');
});

it('only renders the wizard steps once the attendee answers yes', function () {
    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $form->steps()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'sort' => 0],
    ]);

    $schema = app(GenerateEventRegistrationFormKitSchema::class)($form->refresh());

    $wizardWrapper = $schema['children'][2];

    expect($wizardWrapper['if'])->toBe('$get(attending).value === "yes"')
        ->and($wizardWrapper['children'][0]['attrs']['class'])->toBe('form-body');
});
