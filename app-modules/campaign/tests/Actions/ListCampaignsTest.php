<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Tests\Actions;

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;

use AdvisingApp\Campaign\Models\Campaign;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Filament\Resources\CampaignResource\Pages\ListCampaigns;

it('can filter campaigns by `My Campaigns`', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    asSuperAdmin($user);

    $expectedCampaign = Campaign::factory()
        ->for($user, 'user')
        ->create();

    $filteredOutCampaigns = Campaign::factory()->count(2)->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            $expectedCampaign,
            ...$filteredOutCampaigns,
        ])
        ->filterTable('My Campaigns')
        ->assertCanSeeTableRecords([$expectedCampaign])
        ->assertCanNotSeeTableRecords($filteredOutCampaigns);
});

it('can filter campaigns by `Enabled`', function () {
    asSuperAdmin();

    $enabledCampaigns = Campaign::factory()->count(2)->enabled()->create();
    $disabledCampaigns = Campaign::factory()->count(2)->disabled()->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            ...$enabledCampaigns,
            ...$disabledCampaigns,
        ])
        ->filterTable('Enabled')
        ->assertCanSeeTableRecords($enabledCampaigns)
        ->assertCanNotSeeTableRecords($disabledCampaigns);
});

it('can filter campaigns by `Completed`', function () {
    asSuperAdmin();
    $campaign = Campaign::factory()->create();
    CampaignAction::factory()->for($campaign)->successfulExecution()->create();
    $completedCampaigns = Campaign::whereHas('actions', function (Builder $query) {
        $query->whereNotNull('successfully_executed_at');
    })->get();
    $incompleteCampaigns = Campaign::whereDoesntHave('actions', function (Builder $query) {
        $query->whereNotNull('successfully_executed_at');
    })->get();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords($completedCampaigns)
        ->filterTable('Completed')
        ->assertCanSeeTableRecords($completedCampaigns)
        ->assertCanNotSeeTableRecords($incompleteCampaigns);
});
