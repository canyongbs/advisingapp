<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Pages\ProspectInteractionReport;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionStats;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::RecruitmentCrm,
];
$permission = [
    'report-library.view-any',
];

it('cannot render without a license', function () use ($permission) {
    actingAs(user(
        permissions: $permission
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $permission) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permission
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertSuccessful();
});

it('Check total interactions', function () {
    $interactionCount = rand(1, 10);
    $prospectInteractionStats = new ProspectInteractionStats();
    $prospectInteractionStats->cacheTag = 'report-prospect-interaction';

    Prospect::factory()
        ->has(Interaction::factory()->count($interactionCount), 'interactions')
        ->create();

    $stats = $prospectInteractionStats->getStats();
    $totalProspectInteractionsStat = $stats[0];
    expect($totalProspectInteractionsStat->getValue())->toEqual($interactionCount);
});

it('Check unique prospects with interactions', function () {
    $interactionCount = rand(1, 10);
    $prospectInteractionStats = new ProspectInteractionStats();
    $prospectInteractionStats->cacheTag = 'report-prospect-interaction';

    Prospect::factory()
        ->count($interactionCount)
        ->has(Interaction::factory()->count(1), 'interactions')
        ->create();

    $stats = $prospectInteractionStats->getStats();
    $totaluniqueProspectInteractionsStat = $stats[1];
    expect($totaluniqueProspectInteractionsStat->getValue())->toEqual($interactionCount);
});
