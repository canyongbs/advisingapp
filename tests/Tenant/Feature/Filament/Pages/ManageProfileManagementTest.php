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

use App\Filament\Pages\ManageProfileManagement;
use App\Models\Pronouns;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
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

describe('tabs', function () use ($getTabLabels): void {
    it('shows only the Personal Pronouns tab to a user who can view pronouns', function () use ($getTabLabels): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $component = Livewire::test(ManageProfileManagement::class)->assertOk();

        expect($getTabLabels($component))->toBe(['Personal Pronouns']);
    });

    it('opens the first visible tab when no tab is requested', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageProfileManagement::class)
            ->assertOk()
            ->assertSet('activeTab', 'personal-pronouns');
    });

    it('opens the first visible tab when the requested tab is unknown', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::withQueryParams(['tab' => 'does-not-exist'])
            ->test(ManageProfileManagement::class)
            ->assertOk()
            ->assertSet('activeTab', 'personal-pronouns');
    });

    it('keeps the requested tab when it is visible', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::withQueryParams(['tab' => 'personal-pronouns'])
            ->test(ManageProfileManagement::class)
            ->assertOk()
            ->assertSet('activeTab', 'personal-pronouns');
    });
});

describe('personal pronouns table', function (): void {
    it('lists the existing pronouns', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $pronouns = Pronouns::factory(2)->create();

        Livewire::test(ManageProfileManagement::class)
            ->assertCanSeeTableRecords($pronouns);
    });

    it('creates a pronoun for a user with `settings.create`', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.create']);

        actingAs($user);

        expect(Pronouns::query()->where('label', 'They/Them')->exists())->toBeFalse();

        Livewire::test(ManageProfileManagement::class)
            ->callAction(TestAction::make('create')->table(), ['label' => 'They/Them'])
            ->assertHasNoActionErrors();

        expect(Pronouns::query()->where('label', 'They/Them')->exists())->toBeTrue();
    });

    it('validates the label when creating a pronoun', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.create']);

        actingAs($user);

        Livewire::test(ManageProfileManagement::class)
            ->callAction(TestAction::make('create')->table(), ['label' => ''])
            ->assertHasActionErrors(['label' => 'required']);

        expect(Pronouns::query()->count())->toBe(0);
    });

    it('edits a pronoun for a user with `settings.*.update`', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

        actingAs($user);

        $pronoun = Pronouns::factory()->create(['label' => 'She/Her']);

        Livewire::test(ManageProfileManagement::class)
            ->callAction(TestAction::make('edit')->table($pronoun), ['label' => 'He/Him'])
            ->assertHasNoActionErrors();

        expect($pronoun->refresh()->label)->toBe('He/Him');
    });

    it('deletes a pronoun for a user with `settings.*.delete`', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.delete']);

        actingAs($user);

        $pronoun = Pronouns::factory()->create();

        Livewire::test(ManageProfileManagement::class)
            ->callAction(TestAction::make('delete')->table($pronoun));

        assertSoftDeleted($pronoun);
    });

    it('bulk deletes pronouns for a user with `settings.*.delete`', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.delete']);

        actingAs($user);

        $pronouns = Pronouns::factory(3)->create();

        Livewire::test(ManageProfileManagement::class)
            ->assertActionVisible(TestAction::make('delete')->table()->bulk())
            ->selectTableRecords($pronouns)
            ->callAction(TestAction::make('delete')->table()->bulk());

        $pronouns->each(fn (Pronouns $pronoun) => assertSoftDeleted($pronoun));
    });
});

describe('authorization', function (): void {
    it('denies access to the page without the `settings.view-any` permission', function (): void {
        actingAs(User::factory()->create());

        get(ManageProfileManagement::getUrl())
            ->assertForbidden();
    });

    it('allows access to the page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        get(ManageProfileManagement::getUrl())
            ->assertSuccessful();
    });

    it('allows a super admin to access the page', function (): void {
        asSuperAdmin();

        get(ManageProfileManagement::getUrl())
            ->assertSuccessful();
    });

    it('cannot see any write action', function (): void {
        $pronoun = Pronouns::factory()->create();

        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.view']);

        actingAs($user);

        Livewire::test(ManageProfileManagement::class)
            ->assertCanSeeTableRecords([$pronoun])
            ->assertActionHidden(TestAction::make('create')->table())
            ->assertActionHidden(TestAction::make('edit')->table($pronoun))
            ->assertActionHidden(TestAction::make('delete')->table($pronoun))
            ->assertActionHidden(TestAction::make('delete')->table()->bulk());
    });

    it('cannot mount or run a write action by calling the Livewire methods directly', function (): void {
        $pronoun = Pronouns::factory()->create(['label' => 'She/Her']);

        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.view']);

        actingAs($user);

        $recordContext = ['table' => true, 'recordKey' => $pronoun->getKey()];

        Livewire::test(ManageProfileManagement::class)
            ->call('mountAction', 'create', [], ['table' => true])
            ->assertSet('mountedActions', [])
            ->call('mountAction', 'edit', [], $recordContext)
            ->assertSet('mountedActions', [])
            ->call('mountAction', 'delete', [], $recordContext)
            ->assertSet('mountedActions', [])
            ->call('mountAction', 'delete', [], ['table' => true, 'bulk' => true])
            ->assertSet('mountedActions', []);

        expect(Pronouns::query()->count())->toBe(1)
            ->and($pronoun->refresh()->label)->toBe('She/Her')
            ->and($pronoun->trashed())->toBeFalse();
    });
});
