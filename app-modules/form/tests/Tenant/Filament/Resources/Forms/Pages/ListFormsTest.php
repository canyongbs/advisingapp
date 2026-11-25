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
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ListForms;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormEmailAutoReply;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Models\SubmissibleField;
use App\Features\FormDuplicationAccessSettingsFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;
use function Tests\referencedFieldIds;

$listFormsTestUser = function (): User {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    return User::factory()->licensed(LicenseType::cases())->create();
};

$customBlock = fn (SubmissibleField $field): array => [
    'type' => 'customBlock',
    'attrs' => [
        'id' => $field->type,
        'config' => [
            'fieldId' => $field->getKey(),
            'label' => $field->label,
            'isRequired' => $field->is_required,
            ...$field->config,
        ],
    ],
];

it('the create action is gated by the create permission', function () use ($listFormsTestUser): void {
    $user = $listFormsTestUser();
    $user->givePermissionTo('form.view-any');

    actingAs($user);

    Livewire::test(ListForms::class)
        ->assertActionHidden('create');

    $user->givePermissionTo('form.create');

    Livewire::test(ListForms::class)
        ->assertActionVisible('create');
});

it('redirects to the create page with the chosen access settings', function (): void {
    asSuperAdmin();

    Livewire::test(ListForms::class)
        ->callAction(TestAction::make('create'), data: [
            'is_authenticated' => true,
            'generate_prospects' => false,
        ])
        ->assertRedirect(FormResource::getUrl('create', [
            'is_authenticated' => true,
            'generate_prospects' => false,
        ]));
});

it('only lets users with the Recruitment CRM license enable prospect generation on a new form', function (array $licenses, bool $isEnabled): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    $user = User::factory()->licensed($licenses)->create();
    $user->givePermissionTo('form.view-any', 'form.create');

    actingAs($user);

    $component = Livewire::test(ListForms::class)
        ->mountAction(TestAction::make('create'))
        ->assertFormFieldEnabled('is_authenticated');

    $isEnabled
        ? $component->assertFormFieldEnabled('generate_prospects')
        : $component->assertFormFieldDisabled('generate_prospects');
})->with([
    'with the license' => [[LicenseType::RetentionCrm, LicenseType::RecruitmentCrm], true],
    'without the license' => [[LicenseType::RetentionCrm], false],
]);

it('the duplicate action is gated by the create permission', function () use ($listFormsTestUser): void {
    $user = $listFormsTestUser();
    $user->givePermissionTo('form.view-any');

    actingAs($user);

    $form = Form::factory()->create();

    Livewire::test(ListForms::class)
        ->assertTableActionHidden('Duplicate', $form);

    $user->givePermissionTo('form.create');

    Livewire::test(ListForms::class)
        ->assertTableActionVisible('Duplicate', $form);
});

it('can duplicate a form its steps and its fields', function (): void {
    asSuperAdmin();

    // Given that we have a form
    $form = Form::factory()->create();

    expect(Form::count())->toBe(1);

    // And we duplicate it
    Livewire::test(ListForms::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $form);

    // The form, along with all of its content, should be duplicated
    expect(Form::count())->toBe(2);

    $duplicatedForm = Form::where('id', '<>', $form->id)->first();

    expect($duplicatedForm->name)->toBe("Copy - {$form->name}");
    expect($duplicatedForm->fields->count())->toBe($form->fields->count());
    expect($duplicatedForm->steps->count())->toBe($form->steps->count());
});

it('does not allow duplicating a form to a name matching another non-archived form case-insensitively', function (): void {
    asSuperAdmin();

    Form::factory()->create(['name' => 'Existing Form']);
    $form = Form::factory()->create(['name' => 'Some Form']);

    Livewire::test(ListForms::class)
        ->callTableAction('Duplicate', $form, data: ['name' => 'existing form'])
        ->assertHasTableActionErrors(['name' => 'unique']);
});

it('allows duplicating a form to a name freed up by an archived form', function (): void {
    asSuperAdmin();

    $archivedForm = Form::factory()->create(['name' => 'Reusable Name']);
    $archivedForm->archive();

    $form = Form::factory()->create(['name' => 'Some Form']);

    Livewire::test(ListForms::class)
        ->callTableAction('Duplicate', $form, data: ['name' => 'reusable name'])
        ->assertHasNoTableActionErrors();

    expect(Form::query()->whereNull('archived_at')->where('name', 'reusable name')->exists())->toBeTrue();
});

it('will not duplicate form submissions if they exist', function (): void {
    asSuperAdmin();

    // Given that we have a form
    $form = Form::factory()->create();

    $submissionCount = $form->submissions()->count();

    // And we duplicate it
    Livewire::test(ListForms::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $form);

    // The form submissions should not be duplicated
    expect(FormSubmission::count())->toBe($submissionCount);

    $duplicatedForm = Form::where('id', '<>', $form->id)->first();

    expect($duplicatedForm->submissions()->count())->toBe(0);
});

it('displays the correct submissions count for a form', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(5)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    Livewire::test(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 5, $form);
});

it('displays the correct submissions count across all versions', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(3)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $archivedVersion = Form::factory()->create([
        'root_id' => $form->root_id,
        'archived_at' => now(),
    ]);

    FormSubmission::factory()->count(4)->create([
        'form_id' => $archivedVersion->id,
        'submitted_at' => now(),
    ]);

    Livewire::test(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 7, $form);
});

it('does not count submissions from unrelated forms in the submissions count', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(2)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $unrelatedForm = Form::factory()->create();

    FormSubmission::factory()->count(10)->create([
        'form_id' => $unrelatedForm->id,
        'submitted_at' => now(),
    ]);

    Livewire::test(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 2, $form);
});

it('does not count archived submissions in the submissions count', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(5)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $form->submissions()
        ->limit(2)
        ->update(['archived_at' => now()]);

    Livewire::test(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 3, $form);
});

it('archive bulk action archives all selected forms', function (): void {
    asSuperAdmin();

    $formWithSubmissions = Form::factory()->create();

    FormSubmission::factory()->create([
        'form_id' => $formWithSubmissions->id,
        'submitted_at' => now(),
    ]);

    $formWithoutSubmissions = Form::factory()->create();

    $records = collect([$formWithSubmissions, $formWithoutSubmissions]);

    Livewire::test(ListForms::class)
        ->selectTableRecords($records->pluck('id')->all())
        ->callAction(TestAction::make('archive')->table()->bulk())
        ->assertNotified();

    expect($formWithSubmissions->fresh()->archived_at)->not->toBeNull();
    expect($formWithoutSubmissions->fresh()->archived_at)->not->toBeNull();
});

describe('duplication', function () use ($customBlock): void {
    beforeEach(function (): void {
        asSuperAdmin();
    });

    it('gives a duplicated form its own version tree rather than sharing the original', function (): void {
        $form = Form::factory()->create();

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        expect($duplicatedForm->root_id)->toBe($duplicatedForm->getKey())
            ->and($duplicatedForm->root_id)->not->toBe($form->root_id);
    });

    it('points the duplicated form content at its own fields', function () use ($customBlock): void {
        $form = Form::factory()->create();

        [$firstField, $secondField, $thirdField] = $form->fields->all();

        $form->update(['content' => [
            'type' => 'doc',
            'content' => [
                $customBlock($firstField),
                [
                    'type' => 'grid',
                    'attrs' => ['data-cols' => 2],
                    'content' => [
                        ['type' => 'gridColumn', 'content' => [$customBlock($secondField)]],
                        ['type' => 'gridColumn', 'content' => [$customBlock($thirdField)]],
                    ],
                ],
            ],
        ]]);

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        expect(referencedFieldIds($duplicatedForm->content))->toEqualCanonicalizing($duplicatedForm->fields()->pluck('id')->all())
            ->and(referencedFieldIds($form->refresh()->content))->toEqualCanonicalizing($form->fields()->pluck('id')->all());
    });

    it('points the duplicated form step content at its own fields', function () use ($customBlock): void {
        $form = Form::factory()->create(['is_wizard' => true]);
        $form->fields()->delete();
        $form->update(['content' => null]);

        foreach (['First step', 'Second step'] as $sort => $label) {
            $step = $form->steps()->create(['label' => $label, 'sort' => $sort]);

            $field = $step->fields()->create([...FormField::factory()->make()->toArray(), 'form_id' => $form->getKey()]);

            $step->update(['content' => ['type' => 'doc', 'content' => [$customBlock($field)]]]);
        }

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();
        $duplicatedSteps = $duplicatedForm->steps()->get();

        expect($duplicatedSteps)->toHaveCount(2);

        foreach ($duplicatedSteps as $duplicatedStep) {
            expect(referencedFieldIds($duplicatedStep->content))->toEqualCanonicalizing($duplicatedStep->fields()->pluck('id')->all());
        }
    });

    it('does not show the original form submissions count on the duplicated form', function (): void {
        $form = Form::factory()->create();

        FormSubmission::factory()->count(3)->create([
            'form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        Livewire::test(ListForms::class)
            ->assertTableColumnStateSet('submissions_count', 3, record: $form)
            ->assertTableColumnStateSet('submissions_count', 0, record: $duplicatedForm);
    });

    it('copies the email auto reply to the duplicated form without adding one to the original', function (): void {
        $form = Form::factory()->create();

        $subject = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Thanks for submitting']]]]];
        $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'We received your submission.']]]]];

        $form->emailAutoReply()->updateOrCreate([], [
            'subject' => $subject,
            'body' => $body,
            'is_enabled' => true,
        ]);

        expect(FormEmailAutoReply::query()->where('form_id', $form->getKey())->count())->toBe(1);

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();
        $duplicatedEmailAutoReply = $duplicatedForm->emailAutoReply;

        expect(FormEmailAutoReply::query()->where('form_id', $form->getKey())->count())->toBe(1)
            ->and(FormEmailAutoReply::query()->where('form_id', $duplicatedForm->getKey())->count())->toBe(1)
            ->and($duplicatedEmailAutoReply?->subject)->toEqual($subject)
            ->and($duplicatedEmailAutoReply?->body)->toEqual($body)
            ->and($duplicatedEmailAutoReply?->is_enabled)->toBeTrue();
    });

    it('copies the email auto reply images to the duplicated form', function (): void {
        Storage::fake('s3-public');

        $form = Form::factory()->create();

        $emailAutoReply = $form->emailAutoReply()->firstOrFail();
        $image = $emailAutoReply->addMedia(UploadedFile::fake()->image('logo.png'))->toMediaCollection('body', 's3-public');
        $emailAutoReply->update([
            'body' => ['type' => 'doc', 'content' => [['type' => 'image', 'attrs' => ['id' => $image->uuid]]]],
            'is_enabled' => true,
        ]);

        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();
        $duplicatedEmailAutoReply = $duplicatedForm->emailAutoReply()->firstOrFail();
        $duplicatedImage = $duplicatedEmailAutoReply->getFirstMedia('body');

        expect($duplicatedImage)->not->toBeNull()
            ->and($duplicatedImage->uuid)->not->toBe($image->uuid)
            ->and(json_encode($duplicatedEmailAutoReply->body))->toContain($duplicatedImage->uuid)->not->toContain($image->uuid)
            ->and($emailAutoReply->refresh()->getMedia('body')->pluck('uuid')->all())->toBe([$image->uuid])
            ->and(json_encode($emailAutoReply->body))->toContain($image->uuid);

        Storage::disk('s3-public')->assertExists($duplicatedImage->getPathRelativeToRoot());
    });
});

describe('duplication access settings', function () use ($customBlock): void {
    /**
     * A single page form with a mapped email field, an unmapped field, and a grid whose only content in one column is a mapped name field.
     */
    $createFormWithMappedFields = function (array $attributes) use ($customBlock): Form {
        $form = Form::factory()->create($attributes);
        $form->fields()->delete();

        $emailField = $form->fields()->create(['label' => 'Email', 'type' => EducatableEmailFormFieldBlock::type(), 'is_required' => true, 'config' => []]);
        $nameField = $form->fields()->create(['label' => 'Name', 'type' => EducatableNameFormFieldBlock::type(), 'is_required' => true, 'config' => []]);
        $commentsField = $form->fields()->create(['label' => 'Comments', 'type' => 'text_input', 'is_required' => false, 'config' => []]);

        $form->update(['content' => ['type' => 'doc', 'content' => [
            $customBlock($emailField),
            [
                'type' => 'grid',
                'attrs' => ['data-cols' => 2],
                'content' => [
                    ['type' => 'gridColumn', 'content' => [$customBlock($nameField)]],
                    ['type' => 'gridColumn', 'content' => [$customBlock($commentsField)]],
                ],
            ],
        ]]]);

        return $form->refresh();
    };

    $duplicate = function (Form $form, array $data): Form {
        Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form), data: $data)
            ->assertHasNoFormErrors();

        return Form::query()->whereKeyNot($form->getKey())->firstOrFail();
    };

    beforeEach(function (): void {
        asSuperAdmin();
    });

    it('fills the access settings from the original form', function (): void {
        $form = Form::factory()->create(['is_authenticated' => true, 'generate_prospects' => true]);

        Livewire::test(ListForms::class)
            ->mountAction(TestAction::make('Duplicate')->table($form))
            ->assertSchemaStateSet([
                'name' => "Copy - {$form->name}",
                'is_authenticated' => true,
                'generate_prospects' => true,
            ]);
    });

    it('gives the duplicated form the chosen access settings', function () use ($duplicate): void {
        $form = Form::factory()->create(['is_authenticated' => true, 'generate_prospects' => false, 'allow_view_past_submissions' => true]);

        $duplicatedForm = $duplicate($form, ['is_authenticated' => false, 'generate_prospects' => true]);

        expect($duplicatedForm->is_authenticated)->toBeFalse()
            ->and($duplicatedForm->generate_prospects)->toBeTrue()
            ->and($duplicatedForm->allow_view_past_submissions)->toBeFalse()
            ->and($form->refresh()->is_authenticated)->toBeTrue()
            ->and($form->allow_view_past_submissions)->toBeTrue();
    });

    it('does not let a user without the Recruitment CRM license generate prospects on the duplicated form', function () use ($duplicate): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineForms = true;
        $settings->save();

        $user = User::factory()->licensed([LicenseType::RetentionCrm])->create();
        $user->givePermissionTo('form.view-any', 'form.create');

        actingAs($user);

        $form = Form::factory()->create(['is_authenticated' => true, 'generate_prospects' => true]);

        Livewire::test(ListForms::class)
            ->mountAction(TestAction::make('Duplicate')->table($form))
            ->assertSchemaStateSet(['generate_prospects' => false])
            ->assertFormFieldDisabled('generate_prospects');

        $duplicatedForm = $duplicate($form, ['generate_prospects' => true]);

        expect($duplicatedForm->generate_prospects)->toBeFalse();
    });

    it('removes the mapped fields when the duplicated form neither requires authentication nor generates prospects', function () use ($createFormWithMappedFields, $duplicate): void {
        $form = $createFormWithMappedFields(['is_authenticated' => true, 'generate_prospects' => false]);

        $duplicatedForm = $duplicate($form, ['is_authenticated' => false, 'generate_prospects' => false]);

        $grid = $duplicatedForm->content['content'][0];

        expect($duplicatedForm->fields()->pluck('type')->all())->toBe(['text_input'])
            ->and($duplicatedForm->content['content'])->toHaveCount(1)
            ->and($grid['type'])->toBe('grid')
            ->and($grid['content'][0]['content'])->toBe([['type' => 'paragraph']])
            ->and(referencedFieldIds($duplicatedForm->content))->toBe($duplicatedForm->fields()->pluck('id')->all())
            ->and($form->fields()->count())->toBe(3);
    });

    it('removes the mapped fields from the steps of a duplicated multi-step form', function () use ($customBlock, $duplicate): void {
        $form = Form::factory()->create(['is_authenticated' => true, 'is_wizard' => true]);
        $form->fields()->delete();
        $form->update(['content' => null]);

        $step = $form->steps()->create(['label' => 'Contact', 'sort' => 0]);
        $emailField = $step->fields()->create(['form_id' => $form->getKey(), 'label' => 'Email', 'type' => EducatableEmailFormFieldBlock::type(), 'is_required' => true, 'config' => []]);
        $commentsField = $step->fields()->create(['form_id' => $form->getKey(), 'label' => 'Comments', 'type' => 'text_input', 'is_required' => false, 'config' => []]);
        $step->update(['content' => ['type' => 'doc', 'content' => [$customBlock($emailField), $customBlock($commentsField)]]]);

        $duplicatedForm = $duplicate($form, ['is_authenticated' => false, 'generate_prospects' => false]);
        $duplicatedStep = $duplicatedForm->steps()->firstOrFail();

        expect($duplicatedForm->fields()->pluck('type')->all())->toBe(['text_input'])
            ->and(referencedFieldIds($duplicatedStep->content))->toBe($duplicatedStep->fields()->pluck('id')->all());
    });

    it('keeps the mapped fields when the duplicated form requires authentication or generates prospects', function (array $data) use ($createFormWithMappedFields, $duplicate): void {
        $form = $createFormWithMappedFields(['is_authenticated' => true, 'generate_prospects' => false]);

        $duplicatedForm = $duplicate($form, $data);

        expect($duplicatedForm->fields()->count())->toBe(3)
            ->and(referencedFieldIds($duplicatedForm->content))->toEqualCanonicalizing($duplicatedForm->fields()->pluck('id')->all());
    })->with([
        'requires authentication' => [['is_authenticated' => true, 'generate_prospects' => false]],
        'generates prospects' => [['is_authenticated' => false, 'generate_prospects' => true]],
    ]);

    it('warns that mapped fields will be removed only when the original has them and both settings are off', function () use ($createFormWithMappedFields): void {
        $formWithMappedFields = $createFormWithMappedFields(['is_authenticated' => true, 'generate_prospects' => false]);
        $formWithoutMappedFields = Form::factory()->create(['is_authenticated' => true]);

        Livewire::test(ListForms::class)
            ->mountAction(TestAction::make('Duplicate')->table($formWithMappedFields))
            ->assertMountedActionModalDontSee('Mapped fields will be removed')
            ->fillForm(['is_authenticated' => false, 'generate_prospects' => false])
            ->assertMountedActionModalSee('Mapped fields will be removed');

        Livewire::test(ListForms::class)
            ->mountAction(TestAction::make('Duplicate')->table($formWithoutMappedFields))
            ->fillForm(['is_authenticated' => false, 'generate_prospects' => false])
            ->assertMountedActionModalDontSee('Mapped fields will be removed');
    });

    it('opens the edit tab of the duplicated form', function (): void {
        $form = Form::factory()->create();

        $component = Livewire::test(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form));

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        $component->assertRedirect(FormResource::getUrl('view', ['record' => $duplicatedForm, 'tab' => 'edit']));
    });

    // TODO: Cleanup Task (form-duplication-access-settings): delete this test along with `FormDuplicationAccessSettingsFeature`.
    it('keeps the original access settings and actions while the feature is inactive', function () use ($duplicate): void {
        FormDuplicationAccessSettingsFeature::deactivate();

        $form = Form::factory()->create(['is_authenticated' => true, 'generate_prospects' => true]);

        Livewire::test(ListForms::class)
            ->assertActionVisible(TestAction::make('view')->table($form))
            ->assertActionVisible(TestAction::make('edit')->table($form));

        $duplicatedForm = $duplicate($form, ['is_authenticated' => false, 'generate_prospects' => false]);

        expect($duplicatedForm->is_authenticated)->toBeTrue()
            ->and($duplicatedForm->generate_prospects)->toBeTrue();
    });
});
