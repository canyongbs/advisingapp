<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Actions\AttachSubmissionSecret;
use AdvisingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormFieldSubmission;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Models\Secret;
use App\Features\PasswordFormFieldFeature;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

$attachedPasswordSecret = function (string $password = 'hunter2'): Secret {
    $form = Form::factory()->create();
    $field = FormField::factory()->create(['form_id' => $form->getKey(), 'type' => PasswordFormFieldBlock::type()]);
    $submission = FormSubmission::factory()->create(['form_id' => $form->getKey()]);
    $secret = Secret::factory()->create(['value' => Crypt::encryptString($password)]);
    $submission->fields()->attach($field, ['id' => (string) Str::orderedUuid(), 'response' => $secret->getKey()]);
    app(AttachSubmissionSecret::class)($submission, $submission->fields()->firstOrFail()->pivot, $secret->getKey());

    return $secret->refresh();
};

beforeEach(function (): void {
    PasswordFormFieldFeature::activate();
});

it('re-encrypts an attached secret with the submission key', function () use ($attachedPasswordSecret): void {
    $secret = $attachedPasswordSecret();

    $related = $secret->related;

    assert($related instanceof FormFieldSubmission);

    expect($related->submission->secret_key)->not->toBeNull()
        ->and(fn () => Crypt::decryptString($secret->value))->toThrow(DecryptException::class);
});

it('reveals the password to a user who can view the form', function () use ($attachedPasswordSecret): void {
    $secret = $attachedPasswordSecret();

    actingAs(user(licenses: LicenseType::cases(), permissions: ['form.*.view']));

    postJson(route('submission-secrets.reveal'), ['secret_id' => $secret->getKey()])
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertExactJson(['value' => 'hunter2']);
});

it('denies a user who cannot view the form', function () use ($attachedPasswordSecret): void {
    $secret = $attachedPasswordSecret();

    actingAs(user(licenses: LicenseType::cases()));

    postJson(route('submission-secrets.reveal'), ['secret_id' => $secret->getKey()])->assertForbidden();
});

it('does not reveal an unattached secret', function (): void {
    $secret = Secret::factory()->create();

    actingAs(user(licenses: LicenseType::cases(), permissions: ['form.*.view']));

    postJson(route('submission-secrets.reveal'), ['secret_id' => $secret->getKey()])->assertNotFound();
});

it('requires authentication', function () use ($attachedPasswordSecret): void {
    $secret = $attachedPasswordSecret();

    postJson(route('submission-secrets.reveal'), ['secret_id' => $secret->getKey()])->assertUnauthorized();
});
