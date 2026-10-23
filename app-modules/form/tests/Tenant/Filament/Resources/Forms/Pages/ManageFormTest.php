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
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageForm;
use AdvisingApp\Form\Filament\Resources\Forms\RelationManagers\SubmissionsRelationManager;
use AdvisingApp\Form\Filament\Resources\Forms\RelationManagers\WorkflowsRelationManager;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$actingAsUserWithPermissions = function (string ...$permissions): User {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('form.view-any', ...$permissions);
    actingAs($user);

    return $user;
};

$getTabLabels = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $tabs = (new DOMXPath($document))->query('//*[@role="tab"]');
    assert($tabs !== false);

    $labels = [];

    foreach ($tabs as $tab) {
        $labels[] = trim((string) preg_replace('/\s+/', ' ', $tab->textContent));
    }

    return $labels;
};

$getRenderedFormIds = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $forms = $document->getElementsByTagName('form');

    $ids = [];

    foreach ($forms as $form) {
        $ids[] = $form->getAttribute('id');
    }

    return $ids;
};

$archiveAction = fn (): TestAction => TestAction::make('archive');

it('can render the manage form page', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    get(FormResource::getUrl('view', ['record' => $form]))
        ->assertSuccessful();
});

it('displays the form data on the view tab', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $form->name,
            'description' => $form->description,
        ], 'infolist')
        ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
});

describe('tabs', function () use ($actingAsUserWithPermissions, $getTabLabels, $getRenderedFormIds): void {
    it('renders a relation manager only on its own tab', function (string $tab, string $relationManager, string $otherRelationManager): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        get(FormResource::getUrl('view', ['record' => $form]))
            ->assertDontSeeLivewire($relationManager);

        get(FormResource::getUrl('view', ['record' => $form, 'tab' => $tab]))
            ->assertSeeLivewire($relationManager)
            ->assertDontSeeLivewire($otherRelationManager);
    })->with([
        'workflows' => ['workflows', WorkflowsRelationManager::class, SubmissionsRelationManager::class],
        'submissions' => ['submissions', SubmissionsRelationManager::class, WorkflowsRelationManager::class],
    ]);

    it('keeps unsaved edits when switching tabs', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->set('activeRelationManager', 'edit')
            ->fillForm(['name' => 'Unsaved name'])
            ->fillForm(['notify_via_email' => true], 'notificationsForm')
            ->set('activeRelationManager', 'workflows')
            ->set('activeRelationManager', 'edit')
            ->assertSchemaStateSet(['name' => 'Unsaved name'])
            ->assertSchemaStateSet(['notify_via_email' => true], 'notificationsForm');
    });

    it('shows the number of submissions on the submissions tab', function () use ($getTabLabels): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        FormSubmission::factory()->count(2)->create(['form_id' => $form->getKey()]);

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()]);

        expect($getTabLabels($component))->toContain('Submissions 2');
    });

    it('only lists the tabs the user can access', function (array $permissions, bool $isAuthenticated, array $tabs) use ($actingAsUserWithPermissions, $getTabLabels): void {
        $actingAsUserWithPermissions(...$permissions);

        $form = Form::factory()->create(['is_authenticated' => $isAuthenticated]);

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()]);

        expect($getTabLabels($component))->toBe($tabs);
    })->with([
        'view' => [['form.*.view'], true, ['View', 'Workflows', 'Submissions 0']],
        'update' => [['form.*.view', 'form.*.update'], false, ['View', 'Edit', 'On-Screen Response', 'Workflows', 'Submissions 0', 'Notifications']],
        'update on an authenticated form' => [['form.*.view', 'form.*.update'], true, ['View', 'Edit', 'On-Screen Response', 'Workflows', 'Submissions 0', 'Email Auto Reply', 'Notifications']],
    ]);

    it('renders the form of the selected tab only', function (?string $tab, array $formIds) use ($getRenderedFormIds): void {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => true]);

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageForm::class, ['record' => $form->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe($formIds);
    })->with([
        'view' => [null, []],
        'edit' => ['edit', ['form']],
        'on-screen response' => ['on-screen-response', ['onScreenResponseForm']],
        'email auto reply' => ['email-auto-reply', ['emailAutoReplyForm']],
        'notifications' => ['notifications', ['notificationsForm']],
    ]);

    it('does not render a tab the user cannot access', function (string $tab) use ($actingAsUserWithPermissions, $getRenderedFormIds): void {
        $actingAsUserWithPermissions('form.*.view');

        $form = Form::factory()->create(['is_authenticated' => true]);

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageForm::class, ['record' => $form->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe([]);
    })->with(['edit', 'on-screen-response', 'email-auto-reply', 'notifications']);

    it('does not render the email auto reply tab for a form that is not authenticated', function () use ($getRenderedFormIds, $getTabLabels): void {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => false]);

        $component = Livewire::withQueryParams(['tab' => 'email-auto-reply'])
            ->test(ManageForm::class, ['record' => $form->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe([])
            ->and($getTabLabels($component))->not->toContain('Email Auto Reply');
    });
});

describe('edit tab', function () use ($archiveAction): void {
    it('shows the `Archive` action whether or not the form has submissions', function (bool $hasSubmissions) use ($archiveAction): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        if ($hasSubmissions) {
            FormSubmission::factory()->create([
                'form_id' => $form->id,
                'submitted_at' => now(),
            ]);
        }

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->assertActionVisible($archiveAction())
            ->assertActionHasLabel($archiveAction(), 'Archive');
    })->with([
        'with submissions' => true,
        'without submissions' => false,
    ]);

    it('archives the form and redirects to the index when the form has submissions', function () use ($archiveAction): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        FormSubmission::factory()->create([
            'form_id' => $form->id,
            'submitted_at' => now(),
        ]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->callAction($archiveAction())
            ->assertRedirect(FormResource::getUrl('index'));

        expect($form->fresh()->isArchived())->toBeTrue();
    });

    it('redirects to the edit tab of the new version after saving', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['name' => 'Renamed form'])
            ->call('save')
            ->assertHasNoFormErrors();

        $newVersion = Form::query()
            ->where('root_id', $form->root_id)
            ->whereKeyNot($form->getKey())
            ->firstOrFail();

        $component->assertRedirect(FormResource::getUrl('view', ['record' => $newVersion, 'tab' => 'edit']));
    });
});

it('does not allow updating a form to a name matching another non-archived form case-insensitively', function (): void {
    asSuperAdmin();

    Form::factory()->create(['name' => 'Other Form']);
    $form = Form::factory()->create(['name' => 'Editable Form']);

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->fillForm(['name' => 'other form'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows updating a form to a name freed up by an archived form case-insensitively', function (): void {
    asSuperAdmin();

    $archivedForm = Form::factory()->create(['name' => 'Reusable Name']);
    $archivedForm->archive();

    $form = Form::factory()->create(['name' => 'Editable Form']);

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->fillForm(['name' => 'reusable name'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('exposes the mapped block types to the fields rich editor for the custom block badges', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create();

    Livewire::withQueryParams(['tab' => 'edit'])
        ->test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
});

it('persists edits to an existing wizard step\'s description onto the new form version', function (): void {
    asSuperAdmin();

    $form = Form::factory()->create(['is_wizard' => true]);

    $form->steps()->create([
        'label' => 'Original label',
        'description' => 'Original description',
        'content' => ['type' => 'doc', 'content' => []],
    ]);

    $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()]);

    $formData = $component->get('data');
    $stepKey = array_key_first($formData['steps']);
    $formData['steps'][$stepKey]['label'] = 'Updated label';
    $formData['steps'][$stepKey]['description'] = 'Updated description';

    $component
        ->fillForm($formData)
        ->call('save')
        ->assertHasNoFormErrors();

    $newVersion = Form::query()
        ->where('root_id', $form->root_id)
        ->where('id', '!=', $form->id)
        ->firstOrFail();

    expect($newVersion->steps()->first())
        ->label->toBe('Updated label')
        ->description->toBe('Updated description');
});

describe('on-screen response tab', function (): void {
    it('can save the on-screen response without creating a new form version', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create(['on_screen_response' => null]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['on_screen_response' => 'Thanks for submitting!'], 'onScreenResponseForm')
            ->call('saveOnScreenResponse')
            ->assertHasNoFormErrors(form: 'onScreenResponseForm')
            ->assertNotified();

        expect($form->fresh()->on_screen_response)->toBe('Thanks for submitting!')
            ->and(Form::query()->where('root_id', $form->root_id)->count())->toBe(1);
    });
});

describe('email auto reply tab', function (): void {
    $paragraph = fn (string $text): array => [
        'type' => 'doc',
        'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]],
    ];

    it('can save the email auto reply of an authenticated form', function () use ($paragraph): void {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => true]);

        expect($form->emailAutoReply->is_enabled)->toBeFalse()
            ->and($form->emailAutoReply->subject)->toBeNull()
            ->and($form->emailAutoReply->body)->toBeNull();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => true,
                    'subject' => $paragraph('Thanks'),
                    'body' => $paragraph('We received your submission.'),
                ],
            ], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertHasNoFormErrors(form: 'emailAutoReplyForm')
            ->assertNotified();

        $autoReply = $form->fresh()->emailAutoReply;

        expect($autoReply->is_enabled)->toBeTrue()
            ->and($autoReply->subject)->toEqual($paragraph('Thanks'))
            ->and($autoReply->body)->toEqual($paragraph('We received your submission.'))
            ->and(Form::query()->where('root_id', $form->root_id)->count())->toBe(1);
    });

    it('cannot be saved for a form that is not authenticated', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => false]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['is_enabled' => true], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertForbidden();

        expect($form->fresh()->emailAutoReply->is_enabled)->toBeFalse();
    });
});

describe('notifications tab', function (): void {
    it('can save the users to notify', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $userToNotify = User::factory()->licensed(LicenseType::cases())->create();

        expect($form->notificationUsers)->toBeEmpty();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['notification_users' => [$userToNotify->getKey()]], 'notificationsForm')
            ->call('saveNotifications')
            ->assertHasNoFormErrors(form: 'notificationsForm')
            ->assertNotified();

        expect($form->fresh()->notificationUsers->modelKeys())->toBe([$userToNotify->getKey()]);
    });

    it('can enable a notification setting', function (string $setting): void {
        asSuperAdmin();

        $form = Form::factory()->create([$setting => false, 'is_authenticated' => true]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm([$setting => true], 'notificationsForm')
            ->call('saveNotifications')
            ->assertHasNoFormErrors(form: 'notificationsForm');

        expect($form->fresh()->{$setting})->toBeTrue();
    })->with(['notify_to_care_team', 'notify_to_subscribers', 'notify_via_app', 'notify_via_email']);

    it('only shows the care team and subscriber toggles for an authenticated form', function (bool $isAuthenticated): void {
        asSuperAdmin();

        $form = Form::factory()->create(['is_authenticated' => $isAuthenticated]);

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()]);

        foreach (['notify_to_care_team', 'notify_to_subscribers'] as $toggle) {
            $isAuthenticated
                ? $component->assertFormFieldIsVisible($toggle, 'notificationsForm')
                : $component->assertFormFieldIsHidden($toggle, 'notificationsForm');
        }
    })->with([
        'authenticated' => true,
        'not authenticated' => false,
    ]);

    it('does not create a new form version', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['notify_via_email' => true], 'notificationsForm')
            ->call('saveNotifications');

        expect(Form::query()->where('root_id', $form->root_id)->count())->toBe(1)
            ->and($form->fresh()->isArchived())->toBeFalse();
    });
});

describe('authorization', function () use ($actingAsUserWithPermissions, $archiveAction): void {
    it('denies access to the manage form page without the `form.view-any` permission', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineForms = true;
        $settings->save();

        actingAs(User::factory()->licensed(LicenseType::cases())->create());

        $form = Form::factory()->create();

        get(FormResource::getUrl('view', ['record' => $form]))
            ->assertForbidden();
    });

    it('denies access to the manage form page without a license', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('form.view-any', 'form.*.view', 'form.*.update');
        actingAs($user);

        $form = Form::factory()->create();

        get(FormResource::getUrl('view', ['record' => $form]))
            ->assertForbidden();
    });

    it('denies access to the manage form page when online forms are unavailable', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.view', 'form.*.update');

        $form = Form::factory()->create();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineForms = false;
        $settings->save();

        get(FormResource::getUrl('view', ['record' => $form]))
            ->assertForbidden();
    });

    it('allows access to the manage form page with the `form.*.view` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.view');

        $form = Form::factory()->create();

        get(FormResource::getUrl('view', ['record' => $form]))
            ->assertSuccessful();
    });

    it('does not load the editable forms without the `form.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.view');

        $form = Form::factory()->create(['is_authenticated' => true]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->assertSet('data', [])
            ->assertSet('onScreenResponseData', [])
            ->assertSet('emailAutoReplyData', [])
            ->assertSet('notificationsData', []);
    });

    it('does not save without the `form.*.update` permission', function (string $method) use ($actingAsUserWithPermissions): void {
        $user = $actingAsUserWithPermissions('form.*.view', 'form.*.update');

        $form = Form::factory()->create([
            'is_authenticated' => true,
            'notify_via_email' => false,
            'on_screen_response' => null,
        ]);

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['name' => 'Renamed'])
            ->fillForm(['on_screen_response' => 'Changed response'], 'onScreenResponseForm')
            ->fillForm(['is_enabled' => true], 'emailAutoReplyForm')
            ->fillForm(['notify_via_email' => true], 'notificationsForm');

        $user->revokePermissionTo('form.*.update');

        $component->call($method)->assertForbidden();

        expect(Form::query()->where('root_id', $form->root_id)->count())->toBe(1)
            ->and($form->fresh())
            ->name->not->toBe('Renamed')
            ->on_screen_response->toBeNull()
            ->notify_via_email->toBeFalse()
            ->and($form->fresh()->emailAutoReply->is_enabled)->toBeFalse();
    })->with(['save', 'saveOnScreenResponse', 'saveEmailAutoReply', 'saveNotifications']);

    it('does not save a single form component without the `form.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $user = $actingAsUserWithPermissions('form.*.view', 'form.*.update');

        $form = Form::factory()->create();

        $component = Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['name' => 'Renamed']);

        $user->revokePermissionTo('form.*.update');

        $component->call('saveFormComponentOnly', 'data.name')->assertForbidden();

        expect($form->fresh()->name)->not->toBe('Renamed');
    });

    it('allows saving with the `form.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.view', 'form.*.update');

        $paragraph = fn (string $text): array => [
            'type' => 'doc',
            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]],
        ];

        $form = Form::factory()->create([
            'is_authenticated' => true,
            'notify_via_email' => false,
            'on_screen_response' => null,
        ]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->fillForm(['notify_via_email' => true], 'notificationsForm')
            ->call('saveNotifications')
            ->assertNotified()
            ->fillForm(['on_screen_response' => 'Changed response'], 'onScreenResponseForm')
            ->call('saveOnScreenResponse')
            ->assertNotified()
            ->fillForm(['emailAutoReply' => ['is_enabled' => true, 'subject' => $paragraph('Thanks'), 'body' => $paragraph('We received your submission.')]], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertNotified()
            ->fillForm(['name' => 'Renamed'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $form->refresh();
        $newVersion = Form::query()->where('root_id', $form->root_id)->whereKeyNot($form->getKey())->firstOrFail();

        expect($form->isArchived())->toBeTrue()
            ->and($form->notify_via_email)->toBeTrue()
            ->and($form->on_screen_response)->toBe('Changed response')
            ->and($newVersion->name)->toBe('Renamed')
            ->and($newVersion->emailAutoReply->is_enabled)->toBeTrue();
    });

    it('hides the `Archive` action without the `form.*.delete` permission', function () use ($actingAsUserWithPermissions, $archiveAction): void {
        $actingAsUserWithPermissions('form.*.view', 'form.*.update');

        $form = Form::factory()->create();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->assertActionHidden($archiveAction());
    });

    it('hides the header actions without the `form.*.view` permission', function (string $action) use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.update');

        $form = Form::factory()->create(['embed_enabled' => true]);

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->assertActionHidden($action);
    })->with(['preview', 'view', 'embed_snippet']);

    it('hides the `Archive` action without the `form.*.update` permission even when the user can delete', function () use ($actingAsUserWithPermissions, $archiveAction): void {
        $actingAsUserWithPermissions('form.*.view', 'form.*.delete');

        $form = Form::factory()->create();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->assertActionHidden($archiveAction());
    });

    it('does not archive a form through direct invocation without both update and delete permissions', function (string $permission) use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('form.*.view', $permission);

        $form = Form::factory()->create();

        expect($form->isArchived())->toBeFalse();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->call('mountAction', 'archive')
            ->call('callMountedAction')
            ->assertNotNotified();

        expect($form->fresh()->isArchived())->toBeFalse();
    })->with([
        'update only' => 'form.*.update',
        'delete only' => 'form.*.delete',
    ]);

    it('allows archiving a form with both update and delete permissions', function () use ($actingAsUserWithPermissions, $archiveAction): void {
        $actingAsUserWithPermissions('form.*.view', 'form.*.update', 'form.*.delete');

        $form = Form::factory()->create();

        expect($form->isArchived())->toBeFalse();

        Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
            ->callAction($archiveAction())
            ->assertRedirect(FormResource::getUrl('index'));

        expect($form->fresh()->isArchived())->toBeTrue();
    });
});
