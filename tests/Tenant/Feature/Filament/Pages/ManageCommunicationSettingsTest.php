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
use AdvisingApp\Engagement\Livewire\EmailTemplatesTable;
use AdvisingApp\Engagement\Livewire\SmsTemplatesTable;
use App\Filament\Pages\ManageCommunicationSettings;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
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

it('can render the manage communication settings page', function (): void {
    asSuperAdmin();

    get(ManageCommunicationSettings::getUrl())
        ->assertSuccessful();
});

it('loads the notification settings data into the form', function (): void {
    asSuperAdmin();

    $settings = app(NotificationSettings::class);
    $settings->from_name = 'Existing From Name';
    $settings->save();

    Livewire::test(ManageCommunicationSettings::class)
        ->assertSchemaStateSet(['from_name' => 'Existing From Name'], 'notificationSettingsForm');
});

it('can update the notification settings', function (): void {
    asSuperAdmin();

    Livewire::test(ManageCommunicationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ], 'notificationSettingsForm')
        ->call('saveNotificationSettings')
        ->assertHasNoFormErrors();

    $settings = app(NotificationSettings::class);

    expect($settings->from_name)->toBe('New From Name')
        ->and($settings->primary_color)->toBe(Color::Blue);
});

it('validates the inputs', function (array $state, array $errors): void {
    asSuperAdmin();

    Livewire::test(ManageCommunicationSettings::class)
        ->fillForm($state, 'notificationSettingsForm')
        ->call('saveNotificationSettings')
        ->assertHasFormErrors($errors, 'notificationSettingsForm');
})->with([
    'from_name max' => [['from_name' => str_repeat('a', 151)], ['from_name' => 'max']],
]);

describe('tabs', function () use ($getTabLabels, $getRenderedFormIds): void {
    it('only lists the tabs the user can access', function (array $licenses, array $permissions, array $tabs) use ($getTabLabels): void {
        $user = User::factory()->licensed($licenses)->create();
        $user->givePermissionTo(...$permissions);

        actingAs($user);

        $component = Livewire::test(ManageCommunicationSettings::class);

        expect($getTabLabels($component))->toBe($tabs);
    })->with([
        'settings viewer without a license' => [[], ['settings.view-any'], ['Notifications']],
        'settings viewer with a crm license' => [LicenseType::cases(), ['settings.view-any'], ['Notifications', 'Email', 'Text']],
    ]);

    it('lists every tab for a super admin', function () use ($getTabLabels): void {
        asSuperAdmin();

        expect($getTabLabels(Livewire::test(ManageCommunicationSettings::class)))
            ->toBe(['Notifications', 'Email', 'Text']);
    });

    it('renders the template table of the selected tab only', function (string $tab, string $table, string $otherTable): void {
        asSuperAdmin();

        get(ManageCommunicationSettings::getUrl(['tab' => $tab]))
            ->assertSuccessful()
            ->assertSeeLivewire($table)
            ->assertDontSeeLivewire($otherTable);
    })->with([
        'email' => ['email', EmailTemplatesTable::class, SmsTemplatesTable::class],
        'text' => ['text', SmsTemplatesTable::class, EmailTemplatesTable::class],
    ]);

    it('renders the notification settings form only on its tab', function () use ($getRenderedFormIds): void {
        asSuperAdmin();

        $onNotifications = Livewire::withQueryParams(['tab' => 'notifications'])
            ->test(ManageCommunicationSettings::class)
            ->assertSet('activeTab', 'notifications')
            ->assertDontSeeLivewire(EmailTemplatesTable::class)
            ->assertDontSeeLivewire(SmsTemplatesTable::class);

        $onEmail = Livewire::withQueryParams(['tab' => 'email'])
            ->test(ManageCommunicationSettings::class)
            ->assertSet('activeTab', 'email');

        expect($getRenderedFormIds($onNotifications))->toContain('notificationSettingsForm')
            ->and($getRenderedFormIds($onEmail))->not->toContain('notificationSettingsForm');
    });

    it('opens the first visible tab when the requested tab is hidden', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::withQueryParams(['tab' => 'email'])
            ->test(ManageCommunicationSettings::class)
            ->assertSet('activeTab', 'notifications')
            ->assertDontSeeLivewire(EmailTemplatesTable::class);
    });

    it('opens the first visible tab when the requested tab is unknown', function (): void {
        asSuperAdmin();

        Livewire::withQueryParams(['tab' => 'does-not-exist'])
            ->test(ManageCommunicationSettings::class)
            ->assertSet('activeTab', 'notifications');
    });
});

describe('authorization', function (): void {
    it('denies access to the page without the `settings.view-any` permission', function (): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();

        actingAs($user);

        get(ManageCommunicationSettings::getUrl())
            ->assertForbidden();
    });

    it('allows access to the page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        get(ManageCommunicationSettings::getUrl())
            ->assertSuccessful();
    });

    it('allows a super admin to access the page', function (): void {
        asSuperAdmin();

        get(ManageCommunicationSettings::getUrl())
            ->assertSuccessful();
    });

    it('disables the notification settings form without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageCommunicationSettings::class)
            ->assertFormFieldDisabled('from_name', 'notificationSettingsForm');

        $user->givePermissionTo('settings.*.update');

        Livewire::test(ManageCommunicationSettings::class)
            ->assertFormFieldEnabled('from_name', 'notificationSettingsForm');
    });

    it('does not save the notification settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageCommunicationSettings::class)
            ->fillForm([
                'from_name' => 'New From Name',
                'primary_color' => Color::Blue->value,
            ], 'notificationSettingsForm')
            ->call('saveNotificationSettings')
            ->assertForbidden();

        $settings = app(NotificationSettings::class);

        expect($settings->from_name)->not->toBe('New From Name')
            ->and($settings->primary_color)->not->toBe(Color::Blue);
    });
});
