<?php

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
    asSuperAdmin();
    Campaign::factory()->create(['user_id' => $user->getKey()]);
    Campaign::factory()->count(2)->create(['user_id' => User::factory()->create()->getKey()]);

    $query = Campaign::query();
    $filterQuery = $query->where('user_id', $user->getKey());
    $filteredCampaigns = $filterQuery->get();
    expect($filteredCampaigns)->toHaveCount(1);
    expect($filteredCampaigns->first()->user_id)->toBe($user->getKey());
});

it('can filter campaigns by `enabled`', function () {
    asSuperAdmin();
    $campaigns = Campaign::factory()->count(10)->create();
    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords($campaigns)
        ->filterTable('Enabled')
        ->assertCanSeeTableRecords($campaigns->where('enabled', true))
        ->assertCanNotSeeTableRecords($campaigns->where('enabled', false));
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
