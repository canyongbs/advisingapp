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
use AdvisingApp\Application\Filament\Resources\Applications\Pages\EditApplication;
use AdvisingApp\Application\Models\Application;
use Livewire\Livewire;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

it('loads existing wizard steps in the embedded application editor', function () {
    asSuperAdmin();

    $application = Application::factory()->create(['is_wizard' => true]);
    $application->steps()->create([
        'label' => 'Existing wizard step',
        'description' => 'Existing step description',
        'content' => ['type' => 'doc', 'content' => []],
    ]);

    $component = Livewire::test(EditApplication::class, ['record' => $application])
        ->assertSuccessful();

    $component->assertSet('data.name', $application->name);
    $steps = $component->get('data.steps');
    assert(is_array($steps));

    expect(array_column($steps, 'label'))->toContain('Existing wizard step');
});

it('persists edits to an existing wizard step\'s description onto the new application version', function () {
    asSuperAdmin();

    $application = Application::factory()->create(['is_wizard' => true]);

    // The factory auto-creates a submission for realism, which would disable
    // the `is_wizard` toggle (`disabled(fn (?Application $record) => $record?->submissions()->exists())`)
    // and strip it from the dehydrated form state, unrelated to what this test covers.
    $application->submissions()->delete();

    $application->steps()->create([
        'label' => 'Original label',
        'description' => 'Original description',
        'content' => ['type' => 'doc', 'content' => []],
    ]);

    $component = Livewire::test(EditApplication::class, ['record' => $application->getRouteKey()]);

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
        ->where('id', '!=', $application->id)
        ->firstOrFail();

    $editUrl = ApplicationResource::getUrl('view', ['record' => $newVersion, 'tab' => 'edit']);

    $component->assertRedirect($editUrl);
    get($editUrl)->assertSuccessful();

    expect($newVersion->steps()->first())
        ->label->toBe('Updated label')
        ->description->toBe('Updated description');

    expect($application->fresh()->isArchived())->toBeTrue()
        ->and($application->steps()->first()->description)->toBe('Original description')
        ->and($component->get('record')->getKey())->toBe($newVersion->getKey());
});

it('persists uploaded rich editor images on the new application version', function (bool $isWizard, bool $isNewStep, bool $hasExistingStep = false) {
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
    $persistedApplication = $application->fresh();
    expect($persistedApplication->wasRecentlyCreated)->toBeFalse();
    $component = livewire(EditApplication::class, ['record' => $persistedApplication]);
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
        ->where('id', '!=', $application->id)
        ->firstOrFail();
    $newOwner = $isWizard ? $newVersion->steps()->orderBy('sort')->firstOrFail() : $newVersion;
    $newMedia = $newOwner->getFirstMedia('content');

    expect($newMedia)->not->toBeNull();
    assert($newMedia !== null);

    expect(data_get($newOwner->content, 'content.0.attrs.id'))->toBe($newMedia->uuid)
        ->and($newMedia->uuid)->not->toBe('uploaded-image')
        ->and($newMedia->uuid)->not->toBe($originalOwner->getFirstMedia('content')?->uuid)
        ->and($originalOwner->fresh()->content)->toBe($originalContent)
        ->and($application->steps()->pluck('id')->all())->toBe($originalStepIds)
        ->and($newVersion->steps()->count())->toBe($isWizard ? ($hasExistingStep ? 2 : 1) : 0);
    $disk->assertExists($newMedia->getPathRelativeToRoot());
})->with([
    'single-step' => [false, false],
    'existing wizard step' => [true, false],
    'new wizard step' => [true, true],
    'new wizard step reordered before an existing step' => [true, true, true],
]);

it('archive action is always visible and labeled Archive', function () {
    asSuperAdmin();
    $applicationWithSubmissions = Application::factory()->create();
    ApplicationSubmission::factory()->create(['application_id' => $applicationWithSubmissions->id]);
    $applicationWithoutSubmissions = Application::factory()->create();
    $applicationWithoutSubmissions->submissions()->delete();

    Livewire::test(EditApplication::class, ['record' => $applicationWithSubmissions])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Archive');

    Livewire::test(EditApplication::class, ['record' => $applicationWithoutSubmissions])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Archive');
});

it('archive action archives the application and redirects to the index when the application has submissions', function () {
    asSuperAdmin();
    $application = Application::factory()->create();

    Livewire::test(EditApplication::class, ['record' => $application])
        ->callAction('archive')
        ->assertRedirect(ApplicationResource::getUrl('index'));

    expect($application->fresh()->isArchived())->toBeTrue();
});

it('exposes the mapped block types to the fields rich editor for the custom block badges', function () {
    asSuperAdmin();
    $application = Application::factory()->create();

    Livewire::test(EditApplication::class, ['record' => $application])
        ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
});

describe('authorization', function () {
    it('denies the embedded editor without update access and allows it with update access', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        actingAs($user);
        $application = Application::factory()->create();

        Livewire::test(EditApplication::class, ['record' => $application])->assertForbidden();

        $user->givePermissionTo('application.view-any', 'application.*.update');

        Livewire::test(EditApplication::class, ['record' => $application])->assertSuccessful();
    });

    it('denies the embedded editor when the admissions feature is unavailable', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.update');
        actingAs($user);
        $application = Application::factory()->create();
        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineAdmissions = false;
        $settings->save();

        Livewire::test(EditApplication::class, ['record' => $application])->assertForbidden();

        $settings->data->addons->onlineAdmissions = true;
        $settings->save();

        Livewire::test(EditApplication::class, ['record' => $application])->assertSuccessful();
    });
    it('denies direct editor access to a view-only user', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);
        $application = Application::factory()->create();

        Livewire::test(EditApplication::class, ['record' => $application])->assertForbidden();
    });
    it('does not expose the archive action without delete permission', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.update');
        actingAs($user);
        $application = Application::factory()->create();

        Livewire::test(EditApplication::class, ['record' => $application])
            ->assertActionHidden('archive')
            ->call('mountAction', 'archive')
            ->call('callMountedAction');

        expect($application->fresh()->isArchived())->toBeFalse();
    });
    it('does not save after update permission is revoked', function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.update');
        actingAs($user);

        $application = Application::factory()->create();
        $versionCount = Application::query()->where('root_id', $application->root_id)->count();
        $component = Livewire::test(EditApplication::class, ['record' => $application]);

        $user->revokePermissionTo('application.*.update');

        $component->call('save')->assertForbidden();

        expect($application->fresh()->isArchived())->toBeFalse()
            ->and(Application::query()->where('root_id', $application->root_id)->count())->toBe($versionCount);
    });
});
