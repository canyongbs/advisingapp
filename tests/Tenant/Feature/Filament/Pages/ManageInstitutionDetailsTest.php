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

use App\Enums\InstitutionDetailsTab;
use App\Filament\Pages\ManageInstitutionDetails;
use App\Models\User;
use App\Settings\CollegeBrandingSettings;
use App\Settings\DisplaySettings;
use App\Settings\InstitutionDetailsSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Actions\Testing\TestAction;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

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

$saveAction = fn (string $handler, string $tab, string $schema): TestAction => TestAction::make($handler)
    ->schemaComponent("tabs.{$tab}.{$schema}-actions", schema: 'content');

it('can render the manage institution details page', function (): void {
    asSuperAdmin();

    get(ManageInstitutionDetails::getUrl())
        ->assertSuccessful();
});

it('loads the existing settings data into the forms', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $institution = app(InstitutionDetailsSettings::class);
    $institution->name = 'Canyon University';
    $institution->ipeds_id = '123456';
    $institution->save();

    $branding = app(CollegeBrandingSettings::class);
    $branding->is_enabled = true;
    $branding->college_text = 'Go Lions';
    $branding->color = Color::Blue;
    $branding->save();

    $display = app(DisplaySettings::class);
    $display->timezone = 'America/Chicago';
    $display->save();

    Livewire::test(ManageInstitutionDetails::class)
        ->assertSchemaStateSet([
            'name' => 'Canyon University',
            'ipeds_id' => '123456',
        ], 'profileForm')
        ->assertSchemaStateSet([
            'is_enabled' => true,
            'college_text' => 'Go Lions',
        ], 'brandingForm')
        ->assertSchemaStateSet([
            'timezone' => 'America/Chicago',
        ], 'displayForm');
});

it('can update the profile settings', function (): void {
    asSuperAdmin();

    $settings = app(InstitutionDetailsSettings::class);
    $settings->name = 'Old Institution Name';
    $settings->save();

    Livewire::test(ManageInstitutionDetails::class)
        ->fillForm(['name' => 'New Institution Name'], 'profileForm')
        ->call('saveProfile')
        ->assertHasNoFormErrors(form: 'profileForm')
        ->assertNotified();

    expect(app(InstitutionDetailsSettings::class)->name)->toBe('New Institution Name');
});

it('can update the branding settings', function (): void {
    asSuperAdmin();

    Livewire::test(ManageInstitutionDetails::class)
        ->fillForm([
            'is_enabled' => true,
            'dismissible' => true,
            'college_text' => 'Go Lions',
            'color' => Color::Red->value,
        ], 'brandingForm')
        ->call('saveBranding')
        ->assertHasNoFormErrors(form: 'brandingForm')
        ->assertNotified()
        ->assertRedirect(ManageInstitutionDetails::getUrl(['tab' => InstitutionDetailsTab::Branding->value]));

    $settings = app(CollegeBrandingSettings::class);

    expect($settings->is_enabled)->toBeTrue()
        ->and($settings->college_text)->toBe('Go Lions')
        ->and($settings->color)->toBe(Color::Red);
});

it('can update the display settings', function (): void {
    asSuperAdmin();

    Livewire::test(ManageInstitutionDetails::class)
        ->fillForm(['timezone' => 'America/New_York'], 'displayForm')
        ->call('saveDisplay')
        ->assertHasNoFormErrors(form: 'displayForm')
        ->assertNotified();

    expect(app(DisplaySettings::class)->timezone)->toBe('America/New_York');
});

describe('branding bar dismissal', function (): void {
    it('resets the branding bar dismissal when branding is not dismissible', function (): void {
        asSuperAdmin();

        $user = User::factory()->create(['is_branding_bar_dismissed' => true]);

        expect($user->is_branding_bar_dismissed)->toBeTrue();

        Livewire::test(ManageInstitutionDetails::class)
            ->fillForm([
                'is_enabled' => true,
                'dismissible' => false,
                'college_text' => 'Go Lions',
                'color' => Color::Blue->value,
            ], 'brandingForm')
            ->call('saveBranding')
            ->assertHasNoFormErrors(form: 'brandingForm');

        expect($user->refresh()->is_branding_bar_dismissed)->toBeFalse();
    });

    it('does not reset the branding bar dismissal when branding is dismissible', function (): void {
        asSuperAdmin();

        $user = User::factory()->create(['is_branding_bar_dismissed' => true]);

        Livewire::test(ManageInstitutionDetails::class)
            ->fillForm([
                'is_enabled' => true,
                'dismissible' => true,
                'college_text' => 'Go Lions',
                'color' => Color::Blue->value,
            ], 'brandingForm')
            ->call('saveBranding')
            ->assertHasNoFormErrors(form: 'brandingForm');

        expect($user->refresh()->is_branding_bar_dismissed)->toBeTrue();
    });
});

describe('tabs', function () use ($getTabLabels, $getRenderedFormIds): void {
    it('lists all tabs for a user with the `settings.view-any` permission', function () use ($getTabLabels): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        expect($getTabLabels(Livewire::test(ManageInstitutionDetails::class)))
            ->toBe(['Profile', 'Branding', 'Dates and Times']);
    });

    it('opens the requested tab', function (): void {
        asSuperAdmin();

        Livewire::withQueryParams(['tab' => InstitutionDetailsTab::Branding->value])
            ->test(ManageInstitutionDetails::class)
            ->assertSet('activeTab', InstitutionDetailsTab::Branding->value);
    });

    it('falls back to the first visible tab when the requested tab is unknown', function (): void {
        asSuperAdmin();

        Livewire::withQueryParams(['tab' => 'does-not-exist'])
            ->test(ManageInstitutionDetails::class)
            ->assertSet('activeTab', InstitutionDetailsTab::Profile->value);
    });

    it('renders the form of the selected tab only', function (string $tab, array $formIds) use ($getRenderedFormIds): void {
        asSuperAdmin();

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageInstitutionDetails::class);

        expect($getRenderedFormIds($component))->toBe($formIds);
    })->with([
        'profile' => ['profile', ['profileForm']],
        'branding' => ['branding', ['brandingForm']],
        'dates and times' => ['dates-and-times', ['displayForm']],
    ]);
});

describe('authorization', function () use ($saveAction): void {
    it('denies access to the page without the `settings.view-any` permission', function (): void {
        actingAs(User::factory()->create());

        get(ManageInstitutionDetails::getUrl())
            ->assertForbidden();
    });

    it('allows access to the page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(ManageInstitutionDetails::getUrl())
            ->assertOk();
    });

    it('allows a super admin to access the page', function (): void {
        asSuperAdmin();

        get(ManageInstitutionDetails::getUrl())
            ->assertOk();
    });

    it('disables the forms without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        Livewire::test(ManageInstitutionDetails::class)
            ->assertFormFieldDisabled('ipeds_id', 'profileForm')
            ->assertFormFieldDisabled('name', 'profileForm')
            ->assertFormFieldDisabled('dark_logo', 'profileForm')
            ->assertFormFieldDisabled('light_logo', 'profileForm')
            ->assertFormFieldDisabled('is_enabled', 'brandingForm')
            ->assertFormFieldDisabled('timezone', 'displayForm');

        $user->givePermissionTo('settings.*.update');

        Livewire::test(ManageInstitutionDetails::class)
            ->assertFormFieldEnabled('name', 'profileForm')
            ->assertFormFieldEnabled('is_enabled', 'brandingForm')
            ->assertFormFieldEnabled('timezone', 'displayForm');
    });

    it('hides the save action without the `settings.*.update` permission', function () use ($saveAction): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        Livewire::test(ManageInstitutionDetails::class)
            ->assertActionDoesNotExist($saveAction('saveProfile', 'profile', 'profileForm'));

        $user->givePermissionTo('settings.*.update');

        Livewire::test(ManageInstitutionDetails::class)
            ->assertActionVisible($saveAction('saveProfile', 'profile', 'profileForm'));
    });

    it('does not save the profile settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $settings = app(InstitutionDetailsSettings::class);
        $settings->name = 'Existing Institution Name';
        $settings->save();

        Livewire::test(ManageInstitutionDetails::class)
            ->fillForm(['name' => 'New Institution Name'], 'profileForm')
            ->call('saveProfile')
            ->assertForbidden();

        expect(app(InstitutionDetailsSettings::class)->name)->toBe('Existing Institution Name');
    });

    it('does not save the branding settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $settings = app(CollegeBrandingSettings::class);
        $settings->is_enabled = true;
        $settings->college_text = 'Existing College Text';
        $settings->color = Color::Blue;
        $settings->save();

        Livewire::test(ManageInstitutionDetails::class)
            ->fillForm(['college_text' => 'New College Text'], 'brandingForm')
            ->call('saveBranding')
            ->assertForbidden();

        expect(app(CollegeBrandingSettings::class)->college_text)->toBe('Existing College Text');
    });

    it('does not save the display settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $settings = app(DisplaySettings::class);
        $settings->timezone = 'America/Chicago';
        $settings->save();

        Livewire::test(ManageInstitutionDetails::class)
            ->fillForm(['timezone' => 'America/New_York'], 'displayForm')
            ->call('saveDisplay')
            ->assertForbidden();

        expect(app(DisplaySettings::class)->timezone)->toBe('America/Chicago');
    });
});
