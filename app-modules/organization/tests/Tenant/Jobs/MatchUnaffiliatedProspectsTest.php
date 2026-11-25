<?php

use AdvisingApp\Organization\Jobs\MatchUnaffiliatedProspects;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use App\Features\OrganizationFeature;

beforeEach(function (): void {
    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();
});

it('affiliates unaffiliated prospects with matching organizations', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $otherOrganization = Organization::factory()->create();

    $matchingProspect = Prospect::factory()->create();
    $matchingProspect->primaryEmailAddress->update(['address' => 'matching@acme.edu']);

    $affiliatedProspect = Prospect::factory()->for($otherOrganization)->create();
    $affiliatedProspect->primaryEmailAddress->update(['address' => 'affiliated@acme.edu']);

    $unmatchedProspect = Prospect::factory()->create();
    $unmatchedProspect->primaryEmailAddress->update(['address' => 'unmatched@unmatched.test']);

    dispatch_sync(new MatchUnaffiliatedProspects());

    expect($matchingProspect->refresh()->organization_id)->toBe($organization->getKey())
        ->and($affiliatedProspect->refresh()->organization_id)->toBe($otherOrganization->getKey())
        ->and($unmatchedProspect->refresh()->organization_id)->toBeNull();
});

it('only affiliates prospects matching the given organization', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $matchingProspect = Prospect::factory()->create();
    $matchingProspect->primaryEmailAddress->update(['address' => 'matching@acme.edu']);

    $otherProspect = Prospect::factory()->create();
    $otherProspect->primaryEmailAddress->update(['address' => 'other@globex.edu']);

    dispatch_sync(new MatchUnaffiliatedProspects($organization->getKey()));

    expect($matchingProspect->refresh()->organization_id)->toBe($organization->getKey())
        ->and($otherProspect->refresh()->organization_id)->toBeNull();
});

it('affiliates a prospect with the organization matching its primary email address when matching for another organization', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $primaryOrganization = Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@globex.edu']);
    ProspectEmailAddress::factory()->for($prospect)->create(['address' => 'person@acme.edu', 'order' => 2]);

    dispatch_sync(new MatchUnaffiliatedProspects($organization->getKey()));

    expect($prospect->refresh()->organization_id)->toBe($primaryOrganization->getKey());
});

it('does not fall back to matching every organization when the given organization no longer exists', function (): void {
    $deletedOrganization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']], 'deleted_at' => now()]);
    Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@globex.edu']);

    dispatch_sync(new MatchUnaffiliatedProspects($deletedOrganization->getKey()));

    expect($prospect->refresh()->organization_id)->toBeNull();
});
