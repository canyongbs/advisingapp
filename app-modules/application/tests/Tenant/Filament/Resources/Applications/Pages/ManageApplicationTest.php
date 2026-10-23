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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplication;
use AdvisingApp\Application\Filament\Resources\Applications\RelationManagers\SubmissionsRelationManager;
use AdvisingApp\Application\Filament\Resources\Applications\RelationManagers\WorkflowsRelationManager;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\AdmissionsAndEventsEmailAutoReplyFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    seed(ApplicationSubmissionStateSeeder::class);
});

$actingAsUserWithPermissions = function (string ...$permissions): User {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('application.view-any', ...$permissions);
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

$richText = fn (string $text): array => [
    'type' => 'doc',
    'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]],
];

it('can render the manage application page', function (): void {
    asSuperAdmin();

    $application = Application::factory()->create();

    get(ApplicationResource::getUrl('view', ['record' => $application]))
        ->assertSuccessful();
});

it('displays the application data', function (): void {
    asSuperAdmin();

    $application = Application::factory()->create();

    Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $application->name,
            'description' => $application->description,
        ], 'infolist')
        ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
});

describe('tabs', function () use ($actingAsUserWithPermissions, $getTabLabels, $getRenderedFormIds): void {
    it('renders a relation manager only on its own tab', function (string $tab, string $relationManager, string $otherRelationManager): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))
            ->assertDontSeeLivewire($relationManager);

        get(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => $tab]))
            ->assertSeeLivewire($relationManager)
            ->assertDontSeeLivewire($otherRelationManager);
    })->with([
        'workflows' => ['workflows', WorkflowsRelationManager::class, SubmissionsRelationManager::class],
        'submissions' => ['submissions', SubmissionsRelationManager::class, WorkflowsRelationManager::class],
    ]);

    it('keeps unsaved edits when switching tabs', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
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

        $application = Application::factory()->create();
        ApplicationSubmission::factory()->count(2)->create(['application_id' => $application->getKey()]);
        $submissionCount = $application->submissions()->count();

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        expect($getTabLabels($component))->toContain("Submissions {$submissionCount}");
    });

    it('only counts the submissions the user is licensed to see on the submissions tab', function () use ($getTabLabels): void {
        $application = Application::factory()->create();
        $application->submissions()->delete();
        ApplicationSubmission::factory()->create([
            'application_id' => $application->getKey(),
            'author_type' => (new Student())->getMorphClass(),
            'author_id' => Student::factory(),
        ]);

        asSuperAdmin();

        expect($getTabLabels(Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])))
            ->toContain('Submissions 1');

        $user = User::factory()->licensed([LicenseType::RecruitmentCrm])->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);

        expect($getTabLabels(Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])))
            ->toContain('Submissions 0');
    });

    it('only lists the tabs the user can access', function (array $permissions, array $tabs) use ($actingAsUserWithPermissions, $getTabLabels): void {
        $actingAsUserWithPermissions(...$permissions);

        $application = Application::factory()->create();
        $application->submissions()->delete();

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        expect($getTabLabels($component))->toBe($tabs);
    })->with([
        'view' => [['application.*.view'], ['View', 'Workflows', 'Submissions 0']],
        'update' => [['application.*.view', 'application.*.update'], ['View', 'Edit', 'Workflows', 'Submissions 0', 'Notifications', 'Email Auto Reply']],
    ]);

    it('renders the form of the selected tab only', function (?string $tab, array $formIds) use ($getRenderedFormIds): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe($formIds);
    })->with([
        'view' => [null, []],
        'edit' => ['edit', ['form']],
        'notifications' => ['notifications', ['notificationsForm']],
        'email auto reply' => ['email-auto-reply', ['emailAutoReplyForm']],
    ]);

    it('does not render a tab the user cannot access', function (string $tab) use ($actingAsUserWithPermissions, $getRenderedFormIds): void {
        $actingAsUserWithPermissions('application.*.view');

        $application = Application::factory()->create();

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe([]);
    })->with(['edit', 'notifications', 'email-auto-reply']);
});

describe('edit tab', function () use ($archiveAction): void {
    it('loads existing wizard steps', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create(['is_wizard' => true]);
        $application->steps()->create([
            'label' => 'Existing wizard step',
            'description' => 'Existing step description',
            'content' => ['type' => 'doc', 'content' => []],
        ]);

        $steps = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->assertSet('data.name', $application->name)
            ->get('data.steps');
        assert(is_array($steps));

        expect(array_column($steps, 'label'))->toContain('Existing wizard step');
    });

    it('saves edits to an existing wizard step onto a new application version', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create(['is_wizard' => true]);

        // The factory creates a submission, which would disable the wizard fields.
        $application->submissions()->delete();

        $application->steps()->create([
            'label' => 'Original label',
            'description' => 'Original description',
            'content' => ['type' => 'doc', 'content' => []],
        ]);

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        $formData = $component->get('data');
        $stepKey = array_key_first($formData['steps']);
        $formData['steps'][$stepKey]['label'] = 'Updated label';
        $formData['steps'][$stepKey]['description'] = 'Updated description';

        $component
            ->fillForm($formData)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $newVersion = Application::query()
            ->where('root_id', $application->root_id)
            ->whereKeyNot($application->getKey())
            ->firstOrFail();

        $component->assertRedirect(ApplicationResource::getUrl('view', ['record' => $newVersion, 'tab' => 'edit']));

        expect($newVersion->steps()->first())
            ->label->toBe('Updated label')
            ->description->toBe('Updated description')
            ->and($application->fresh()->isArchived())->toBeTrue()
            ->and($application->steps()->first()->description)->toBe('Original description');
    });

    it('moves the email auto reply onto the new application version', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();
        $emailAutoReply = $application->emailAutoReply()->firstOrFail();
        $emailAutoReply->update(['is_enabled' => true]);

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['name' => 'Renamed'])
            ->call('save')
            ->assertHasNoFormErrors();

        $newVersion = Application::query()
            ->where('root_id', $application->root_id)
            ->whereKeyNot($application->getKey())
            ->firstOrFail();

        expect($newVersion->emailAutoReply?->getKey())->toBe($emailAutoReply->getKey())
            ->and($newVersion->emailAutoReply?->is_enabled)->toBeTrue()
            ->and($application->fresh()->emailAutoReply)->toBeNull();
    });

    it('does not move the email auto reply onto the new application version when the feature is inactive', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();
        $emailAutoReply = $application->emailAutoReply()->firstOrFail();

        AdmissionsAndEventsEmailAutoReplyFeature::deactivate();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['name' => 'Renamed'])
            ->call('save')
            ->assertHasNoFormErrors();

        $newVersion = Application::query()
            ->where('root_id', $application->root_id)
            ->whereKeyNot($application->getKey())
            ->firstOrFail();

        expect($newVersion->emailAutoReply()->exists())->toBeFalse()
            ->and($emailAutoReply->refresh()->application_id)->toBe($application->getKey());
    });

    it('saves uploaded rich editor images onto the new application version', function (bool $isWizard, bool $isNewStep, bool $hasExistingStep = false): void {
        asSuperAdmin();
        $disk = Storage::fake('s3-public');

        $originalContent = ['type' => 'doc', 'content' => []];
        $application = Application::factory()->create(['is_wizard' => $isWizard, 'content' => $originalContent]);
        $application->submissions()->delete();
        $originalOwner = $isWizard && (! $isNewStep || $hasExistingStep) ? $application->steps()->create([
            'label' => 'Image step',
            'description' => 'Original description',
            'content' => $originalContent,
        ]) : $application;
        $originalStepIds = $application->steps()->pluck('id')->all();
        $originalContent = $originalOwner->fresh()->content;
        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);
        $statePath = 'data.content';

        if ($isNewStep) {
            $component->set('data.steps.new-step', [
                'label' => 'Uploaded image step',
                'description' => 'New step description',
                'content' => ['type' => 'doc', 'content' => []],
            ]);
            $statePath = 'data.steps.new-step.content';

            if ($hasExistingStep) {
                $steps = $component->get('data.steps');
                assert(is_array($steps));
                $component->set('data.steps', ['new-step' => $steps['new-step'], ...$steps]);
            }
        } elseif ($isWizard) {
            $steps = $component->get('data.steps');
            assert(is_array($steps));
            $stepKey = array_key_first($steps);
            $statePath = "data.steps.{$stepKey}.content";
        }

        expect($originalOwner->getMedia('content'))->toBeEmpty();

        $component
            ->set($statePath, [
                'type' => 'doc',
                'content' => [[
                    'type' => 'image',
                    'attrs' => ['id' => 'uploaded-image', 'src' => 'temporary-image.png'],
                ]],
            ])
            ->set("componentFileAttachments.{$statePath}.uploaded-image", UploadedFile::fake()->image('image.png'))
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $newVersion = Application::query()
            ->where('root_id', $application->root_id)
            ->whereKeyNot($application->getKey())
            ->firstOrFail();
        $newOwner = $isWizard ? $newVersion->steps()->orderBy('sort')->firstOrFail() : $newVersion;
        $newMedia = $newOwner->getFirstMedia('content');
        assert($newMedia !== null);

        expect(data_get($newOwner->content, 'content.0.attrs.id'))->toBe($newMedia->uuid)
            ->and($newMedia->uuid)->not->toBe('uploaded-image')
            ->and($originalOwner->fresh()->content)->toBe($originalContent)
            ->and($application->steps()->pluck('id')->all())->toBe($originalStepIds)
            ->and($newVersion->steps()->count())->toBe($isWizard ? ($hasExistingStep ? 2 : 1) : 0);
        $disk->assertExists($newMedia->getPathRelativeToRoot());
    })->with([
        'single-step' => [false, false],
        'existing wizard step' => [true, false],
        'new wizard step' => [true, true],
        'new wizard step before an existing step' => [true, true, true],
    ]);

    it('keeps existing wizard images on their steps when the steps change', function (string $change): void {
        assert(in_array($change, ['reorder', 'insert', 'delete'], true));

        asSuperAdmin();
        $disk = Storage::fake('s3-public');
        $application = Application::factory()->create(['is_wizard' => true]);
        $application->submissions()->delete();
        $originalMediaUuids = [];
        $originalStepIds = [];
        $fileNames = ['First step' => 'first.png', 'Second step' => 'second.png'];

        foreach ($fileNames as $label => $fileName) {
            $step = $application->steps()->create([
                'label' => $label,
                'sort' => count($originalStepIds) + 1,
            ]);

            $media = $step->addMedia(UploadedFile::fake()->image($fileName))->toMediaCollection('content', 's3-public');
            $step->content = [
                'type' => 'doc',
                'content' => [[
                    'type' => 'image',
                    'attrs' => ['id' => $media->uuid, 'src' => $media->getUrl()],
                ]],
            ];
            $step->save();
            $originalMediaUuids[$label] = $media->uuid;
            $originalStepIds[] = $step->getKey();
        }

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);
        $steps = $component->get('data.steps');
        assert(is_array($steps));

        [$changedSteps, $expectedLabels] = match ($change) {
            'reorder' => [array_reverse($steps, preserve_keys: true), ['Second step', 'First step']],
            'insert' => [
                [
                    'new-step' => [
                        'label' => 'New step',
                        'description' => 'No image',
                        'content' => ['type' => 'doc', 'content' => []],
                    ],
                    ...$steps,
                ],
                ['New step', 'First step', 'Second step'],
            ],
            'delete' => [array_diff_key($steps, ["record-{$originalStepIds[0]}" => true]), ['Second step']],
        };

        $component->set('data.steps', $changedSteps)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $newVersion = Application::query()->where('root_id', $application->root_id)->whereKeyNot($application->getKey())->firstOrFail();
        $newSteps = $newVersion->steps()->orderBy('sort')->get();
        expect($newSteps->pluck('label')->all())->toBe($expectedLabels);

        foreach ($newSteps as $step) {
            if ($step->label === 'New step') {
                expect($step->getMedia('content'))->toBeEmpty();

                continue;
            }

            $media = $step->getFirstMedia('content');
            assert($media !== null);

            expect($step->getMedia('content')->pluck('file_name')->all())->toBe([$fileNames[$step->label]])
                ->and(data_get($step->content, 'content.0.attrs.id'))->toBe($media->uuid)
                ->and($media->uuid)->not->toBe($originalMediaUuids[$step->label]);
            $disk->assertExists($media->getPathRelativeToRoot());
        }

        foreach ($application->steps()->get() as $step) {
            expect(data_get($step->content, 'content.0.attrs.id'))->toBe($originalMediaUuids[$step->label])
                ->and($step->getFirstMedia('content')?->uuid)->toBe($originalMediaUuids[$step->label]);
        }
    })->with(['reorder', 'insert', 'delete']);

    it('shows the `Archive` action whether or not the application has submissions', function (bool $hasSubmissions) use ($archiveAction): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        if (! $hasSubmissions) {
            $application->submissions()->delete();
        }

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->assertActionVisible($archiveAction())
            ->assertActionHasLabel($archiveAction(), 'Archive');
    })->with([
        'with submissions' => true,
        'without submissions' => false,
    ]);

    it('archives the application and redirects to the index', function () use ($archiveAction): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        expect($application->isArchived())->toBeFalse();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->callAction($archiveAction())
            ->assertRedirect(ApplicationResource::getUrl('index'));

        expect($application->fresh()->isArchived())->toBeTrue();
    });
});

describe('notifications tab', function (): void {
    it('can save the users to notify', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();
        $userToNotify = User::factory()->licensed(LicenseType::cases())->create();

        expect($application->notificationUsers)->toBeEmpty();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['notification_users' => [$userToNotify->getKey()]], 'notificationsForm')
            ->call('saveNotifications')
            ->assertHasNoFormErrors(form: 'notificationsForm')
            ->assertNotified();

        expect($application->fresh()->notificationUsers->modelKeys())->toBe([$userToNotify->getKey()]);
    });

    it('can enable a notification setting', function (string $setting): void {
        asSuperAdmin();

        $application = Application::factory()->create([$setting => false]);

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([$setting => true], 'notificationsForm')
            ->call('saveNotifications')
            ->assertHasNoFormErrors(form: 'notificationsForm');

        expect($application->fresh()->{$setting})->toBeTrue();
    })->with(['notify_to_care_team', 'notify_to_subscribers', 'notify_via_app', 'notify_via_email']);

    it('does not create a new application version', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['notify_via_email' => true], 'notificationsForm')
            ->call('saveNotifications');

        expect(Application::query()->where('root_id', $application->root_id)->count())->toBe(1)
            ->and($application->fresh()->isArchived())->toBeFalse();
    });
});

describe('email auto reply tab', function () use ($getTabLabels, $richText): void {
    it('displays the email auto reply data', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();
        $application->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->assertSchemaStateSet(['emailAutoReply.is_enabled' => true], 'emailAutoReplyForm');
    });

    it('can save the email auto reply', function () use ($richText): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        expect($application->emailAutoReply()->firstOrFail()->is_enabled)->toBeFalse();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => true,
                    'subject' => $richText('Thanks for applying'),
                    'body' => $richText('We have received your application.'),
                ],
            ], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertHasNoFormErrors(form: 'emailAutoReplyForm')
            ->assertNotified();

        $emailAutoReply = $application->emailAutoReply()->firstOrFail();

        expect($emailAutoReply->is_enabled)->toBeTrue()
            ->and($emailAutoReply->getSubject(null))->toBe('Thanks for applying')
            ->and($emailAutoReply->getBody(null))->toContain('We have received your application.');
    });

    it('creates the email auto reply for an application that does not have one yet', function () use ($richText): void {
        asSuperAdmin();

        $application = Application::factory()->create();
        $application->emailAutoReply()->forceDelete();

        expect($application->emailAutoReply()->exists())->toBeFalse();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => true,
                    'subject' => $richText('Thanks for applying'),
                    'body' => $richText('We have received your application.'),
                ],
            ], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertHasNoFormErrors(form: 'emailAutoReplyForm');

        expect($application->emailAutoReply()->firstOrFail()->is_enabled)->toBeTrue();
    });

    it('saves uploaded images in the email auto reply body', function () use ($richText): void {
        asSuperAdmin();
        $disk = Storage::fake('s3-public');

        $application = Application::factory()->create();
        $statePath = 'emailAutoReplyData.emailAutoReply.body';

        expect($application->emailAutoReply()->firstOrFail()->getMedia('body'))->toBeEmpty();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->set('emailAutoReplyData.emailAutoReply.is_enabled', true)
            ->set('emailAutoReplyData.emailAutoReply.subject', $richText('Thanks for applying'))
            ->set($statePath, [
                'type' => 'doc',
                'content' => [[
                    'type' => 'image',
                    'attrs' => ['id' => 'uploaded-image', 'src' => 'temporary-image.png'],
                ]],
            ])
            ->set("componentFileAttachments.{$statePath}.uploaded-image", UploadedFile::fake()->image('image.png'))
            ->call('saveEmailAutoReply')
            ->assertHasNoFormErrors(form: 'emailAutoReplyForm');

        $emailAutoReply = $application->emailAutoReply()->firstOrFail();
        $media = $emailAutoReply->getFirstMedia('body');
        assert($media !== null);

        expect(data_get($emailAutoReply->body, 'content.0.attrs.id'))->toBe($media->uuid)
            ->and($media->uuid)->not->toBe('uploaded-image');
        $disk->assertExists($media->getPathRelativeToRoot());
    });

    it('validates the inputs', function (array $data, array $errors) use ($richText): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => true,
                    'subject' => $richText('Thanks for applying'),
                    'body' => $richText('We have received your application.'),
                    ...$data,
                ],
            ], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertHasFormErrors($errors, 'emailAutoReplyForm');
    })->with([
        'subject required' => [['subject' => null], ['emailAutoReply.subject' => 'The subject field is required.']],
        'body required' => [['body' => null], ['emailAutoReply.body' => 'The body field is required.']],
    ]);

    it('does not require the subject and body when the email auto reply is disabled', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => false,
                    'subject' => null,
                    'body' => null,
                ],
            ], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply')
            ->assertHasNoFormErrors(form: 'emailAutoReplyForm');
    });

    it('does not create a new application version', function (): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['emailAutoReply' => ['is_enabled' => false]], 'emailAutoReplyForm')
            ->call('saveEmailAutoReply');

        expect(Application::query()->where('root_id', $application->root_id)->count())->toBe(1)
            ->and($application->fresh()->isArchived())->toBeFalse();
    });

    it('does not show or save the email auto reply when the feature is inactive', function () use ($getTabLabels): void {
        asSuperAdmin();

        $application = Application::factory()->create();

        AdmissionsAndEventsEmailAutoReplyFeature::deactivate();

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()]);

        expect($getTabLabels($component))->not->toContain('Email Auto Reply');

        $component->call('saveEmailAutoReply')->assertNotFound();
    });
});

describe('authorization', function () use ($actingAsUserWithPermissions, $archiveAction, $richText): void {
    it('denies access to the manage application page without the `application.*.view` permission', function (): void {
        actingAs(User::factory()->licensed(LicenseType::cases())->create());

        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))
            ->assertForbidden();
    });

    it('denies access to the manage application page without a license', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('application.view-any', 'application.*.view', 'application.*.update');
        actingAs($user);

        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))
            ->assertForbidden();
    });

    it('denies access to the manage application page when online admissions are unavailable', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('application.*.view', 'application.*.update');

        $application = Application::factory()->create();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineAdmissions = false;
        $settings->save();

        get(ApplicationResource::getUrl('view', ['record' => $application]))
            ->assertForbidden();
    });

    it('allows access to the manage application page with the `application.*.view` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('application.*.view');

        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))
            ->assertSuccessful();
    });

    it('does not load the editable forms without the `application.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('application.*.view');

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->assertSet('data', [])
            ->assertSet('notificationsData', [])
            ->assertSet('emailAutoReplyData', []);
    });

    it('does not save without the `application.*.update` permission', function (string $method) use ($actingAsUserWithPermissions): void {
        $user = $actingAsUserWithPermissions('application.*.view', 'application.*.update');

        $application = Application::factory()->create(['notify_via_email' => false]);

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['name' => 'Renamed'])
            ->fillForm(['notify_via_email' => true], 'notificationsForm');

        $user->revokePermissionTo('application.*.update');

        $component->call($method)->assertForbidden();

        expect(Application::query()->where('root_id', $application->root_id)->count())->toBe(1)
            ->and($application->fresh())
            ->name->not->toBe('Renamed')
            ->notify_via_email->toBeFalse();
    })->with(['save', 'saveNotifications']);

    it('does not save the email auto reply without the `application.*.update` permission', function () use ($actingAsUserWithPermissions, $richText): void {
        $user = $actingAsUserWithPermissions('application.*.view', 'application.*.update');

        $application = Application::factory()->create();

        $component = Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm([
                'emailAutoReply' => [
                    'is_enabled' => true,
                    'subject' => $richText('Thanks for applying'),
                    'body' => $richText('We have received your application.'),
                ],
            ], 'emailAutoReplyForm');

        $user->revokePermissionTo('application.*.update');

        $component->call('saveEmailAutoReply')->assertForbidden();

        expect($application->emailAutoReply()->firstOrFail()->is_enabled)->toBeFalse()
            ->and($application->fresh()->isArchived())->toBeFalse();
    });

    it('allows saving with the `application.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('application.*.view', 'application.*.update');

        $application = Application::factory()->create(['notify_via_email' => false]);

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['notify_via_email' => true], 'notificationsForm')
            ->call('saveNotifications')
            ->assertNotified()
            ->fillForm(['name' => 'Renamed'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $application->refresh();

        expect($application->isArchived())->toBeTrue()
            ->and($application->notify_via_email)->toBeTrue()
            ->and(Application::query()->where('root_id', $application->root_id)->whereKeyNot($application->getKey())->value('name'))->toBe('Renamed');
    });

    it('hides the `Archive` action without the `application.*.delete` permission', function () use ($actingAsUserWithPermissions, $archiveAction): void {
        $actingAsUserWithPermissions('application.*.view', 'application.*.update');

        $application = Application::factory()->create();

        Livewire::test(ManageApplication::class, ['record' => $application->getRouteKey()])
            ->assertActionHidden($archiveAction());
    });
});
