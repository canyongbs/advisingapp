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

use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use App\Settings\LicenseSettings;

use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineForms = true;

    $settings->save();
});

test('view endpoint returns an empty schema for an authenticated form', function () {
    // This is the constraint that forces the preview to use a dedicated endpoint:
    // the public entry endpoint must not leak the schema before authentication.
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $form = Form::factory()->create(['is_authenticated' => true]);

    $response = get(route('widgets.forms.api.entry', ['form' => $form]))
        ->assertSuccessful();

    expect($response->json('is_authenticated'))->toBeTrue();
    expect($response->json('schema'))->toBe([]);
});

test('preview endpoint returns a populated schema for an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    expect($form->fields()->count())->toBeGreaterThan(0);

    $response = get(route('forms.api.preview', ['form' => $form]))
        ->assertSuccessful();

    expect($response->json('is_authenticated'))->toBeTrue();
    expect($response->json('schema.children'))->toBeArray()->not->toBeEmpty();
});

test('preview renders the form fields for an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    $form->fields()->delete();

    $firstName = $form->fields()->create([
        'label' => 'What is your first name?',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    $aboutYou = $form->fields()->create([
        'label' => 'Tell us about yourself',
        'type' => 'text_area',
        'is_required' => false,
        'config' => [],
    ]);

    $block = fn (FormField $field): array => [
        'type' => 'customBlock',
        'attrs' => [
            'config' => [
                'fieldId' => $field->id,
                'label' => $field->label,
                'isRequired' => $field->is_required,
            ],
            'id' => $field->type,
        ],
    ];

    $form->content = [
        'type' => 'doc',
        'content' => [$block($firstName), $block($aboutYou)],
    ];

    $form->save();

    get(route('forms.api.preview', ['form' => $form]))
        ->assertSuccessful()
        ->assertJsonFragment(['label' => 'What is your first name?'])
        ->assertJsonFragment(['label' => 'Tell us about yourself']);
});
