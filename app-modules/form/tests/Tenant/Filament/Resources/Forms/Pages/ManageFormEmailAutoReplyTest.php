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
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormEmailAutoReply;
use AdvisingApp\Form\Models\Form;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$richText = fn (string $text): array => [
    'type' => 'doc',
    'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]],
];

it('can render the manage form email auto reply page', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    get(FormResource::getUrl('manage-email-auto-reply', ['record' => $form]))
        ->assertSuccessful();
});

it('displays the email auto reply data', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);
    $form->emailAutoReply->update(['is_enabled' => true]);

    Livewire::test(ManageFormEmailAutoReply::class, ['record' => $form->getKey()])
        ->assertSchemaStateSet([
            'emailAutoReply.is_enabled' => true,
        ]);
});

it('can update the email auto reply', function () use ($richText) {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    expect($form->emailAutoReply->is_enabled)->toBeFalse();

    Livewire::test(ManageFormEmailAutoReply::class, ['record' => $form->getKey()])
        ->fillForm([
            'emailAutoReply' => [
                'is_enabled' => true,
                'subject' => $richText('Thanks for your submission'),
                'body' => $richText('We have received your form.'),
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $emailAutoReply = $form->emailAutoReply()->firstOrFail();

    expect($emailAutoReply->is_enabled)->toBeTrue()
        ->and($emailAutoReply->getSubject(null))->toBe('Thanks for your submission')
        ->and($emailAutoReply->getBody(null))->toContain('We have received your form.');
});

it('validates the inputs', function (array $data, array $errors) use ($richText) {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    Livewire::test(ManageFormEmailAutoReply::class, ['record' => $form->getKey()])
        ->fillForm([
            'emailAutoReply' => [
                'is_enabled' => true,
                'subject' => $richText('Thanks for your submission'),
                'body' => $richText('We have received your form.'),
                ...$data,
            ],
        ])
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'subject required' => [['subject' => null], ['emailAutoReply.subject' => 'The subject field is required.']],
    'body required' => [['body' => null], ['emailAutoReply.body' => 'The body field is required.']],
]);

it('does not require the subject and body when the email auto reply is disabled', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    Livewire::test(ManageFormEmailAutoReply::class, ['record' => $form->getKey()])
        ->fillForm([
            'emailAutoReply' => [
                'is_enabled' => false,
                'subject' => null,
                'body' => null,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->emailAutoReply()->firstOrFail()->is_enabled)->toBeFalse();
});

describe('authorization', function () {
    it('denies access to the page for a form without authentication enabled', function () {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => false]);

        get(FormResource::getUrl('manage-email-auto-reply', ['record' => $form]))
            ->assertForbidden();
    });

    it('denies access to the page without the `form.*.update` permission', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('form.view-any');

        actingAs($user);

        $form = Form::factory()->create(['is_authenticated' => true]);

        get(FormResource::getUrl('manage-email-auto-reply', ['record' => $form]))
            ->assertForbidden();
    });

    it('allows access to the page with the `form.*.update` permission', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('form.view-any');
        $user->givePermissionTo('form.*.update');

        actingAs($user);

        $form = Form::factory()->create(['is_authenticated' => true]);

        get(FormResource::getUrl('manage-email-auto-reply', ['record' => $form]))
            ->assertSuccessful();
    });
});
