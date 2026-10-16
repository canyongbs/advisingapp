<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('redirects a legacy URL to its tab', function (string $page, string $tab) {
    asSuperAdmin();
    $form = Form::factory()->create();

    get(FormResource::getUrl($page, ['record' => $form]))
        ->assertRedirect(FormResource::getUrl('view', ['record' => $form, 'tab' => $tab]));
})->with([
    ['edit', 'edit'],
    ['manage-on-screen-response', 'on-screen-response'],
    ['manage-form-workflows', 'workflows'],
    ['manage-submissions', 'submissions'],
    ['manage-email-auto-reply', 'email-auto-reply'],
    ['manage-notifications', 'notifications'],
]);

it('keeps the query string and targets the submissions table when redirecting a submission link', function () {
    asSuperAdmin();
    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);
    $parameters = ['record' => $form, 'tableAction' => 'view', 'tableActionRecord' => $submission->id];

    get(FormResource::getUrl('manage-submissions', $parameters))
        ->assertRedirect(FormResource::getUrl('view', [...$parameters, 'tab' => 'submissions', 'actionComponent' => 'submissions']));
});

describe('authorization', function () {
    it('denies access to a legacy URL without the `form.view-any` permission', function () {
        actingAs(User::factory()->licensed(LicenseType::cases())->create());
        $form = Form::factory()->create();

        get(FormResource::getUrl('edit', ['record' => $form]))->assertForbidden();
    });
});
