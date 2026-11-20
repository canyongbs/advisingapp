<?php

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
