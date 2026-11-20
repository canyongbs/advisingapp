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
