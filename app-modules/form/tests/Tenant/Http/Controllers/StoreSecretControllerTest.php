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

use AdvisingApp\Form\Models\Secret;
use App\Features\PasswordFormFieldFeature;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    PasswordFormFieldFeature::activate();
});

it('stores the password encrypted without relating it to anything', function (): void {
    $response = postJson(route('widgets.forms.store-secret'), ['value' => 'hunter2'])
        ->assertOk()
        ->assertJsonStructure(['id']);

    $secret = Secret::query()->findOrFail($response->json('id'));

    expect(Crypt::decryptString($secret->value))->toBe('hunter2')
        ->and($secret->value)->not->toContain('hunter2')
        ->and($secret->related_id)->toBeNull();
});

it('replaces the value of a previous unattached secret', function (): void {
    $secret = Secret::factory()->create();

    postJson(route('widgets.forms.store-secret'), [
        'value' => 'new-password',
        'previous_secret_id' => $secret->getKey(),
    ])->assertOk()->assertJson(['id' => $secret->getKey()]);

    expect(Crypt::decryptString($secret->refresh()->value))->toBe('new-password');
});

it('removes a previous secret when no value is given', function (): void {
    $secret = Secret::factory()->create();

    postJson(route('widgets.forms.store-secret'), [
        'value' => null,
        'previous_secret_id' => $secret->getKey(),
    ])->assertOk()->assertJson(['id' => null]);

    expect(Secret::query()->count())->toBe(0);
});

it('does not allow an attached secret to be replaced', function (): void {
    $secret = Secret::factory()->for(Secret::factory()->create(), 'related')->create();

    postJson(route('widgets.forms.store-secret'), [
        'value' => 'new-password',
        'previous_secret_id' => $secret->getKey(),
    ])->assertUnprocessable();
});

it('is not available when the feature is inactive', function (): void {
    PasswordFormFieldFeature::deactivate();

    postJson(route('widgets.forms.store-secret'), ['value' => 'hunter2'])->assertNotFound();

    expect(Secret::query()->exists())->toBeFalse();
});
