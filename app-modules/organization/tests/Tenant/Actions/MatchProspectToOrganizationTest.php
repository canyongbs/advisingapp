<?php

use AdvisingApp\Organization\Actions\MatchProspectToOrganization;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\OrganizationFeature;

beforeEach(function (): void {
    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();
});

it('affiliates a prospect with the organization matching its primary email address', function (string $emailAddress): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => $emailAddress]);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
})->with([
    'same case' => ['person@acme.edu'],
    'different case' => ['person@ACME.edu'],
]);

it('falls back to the first matching additional email address in order', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@unmatched.test']);

    ProspectEmailAddress::factory()->for($prospect)->create(['address' => 'person@globex.edu', 'order' => 3]);
    ProspectEmailAddress::factory()->for($prospect)->create(['address' => 'person@acme.edu', 'order' => 2]);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
});

it('prefers the primary email address over additional email addresses', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@globex.edu']);

    $primaryEmailAddress = ProspectEmailAddress::factory()->for($prospect)->create(['address' => 'person@acme.edu', 'order' => 2]);
    $prospect->primaryEmailAddress()->associate($primaryEmailAddress)->save();

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
});

it('keeps the existing organization of an affiliated prospect', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $organization = Organization::factory()->create();

    $prospect = Prospect::factory()->for($organization)->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@acme.edu']);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
});

it('keeps an organization assigned after the prospect was loaded', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $organization = Organization::factory()->create();

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@acme.edu']);

    $staleProspect = $prospect->fresh();
    assert($staleProspect instanceof Prospect);

    $prospect->organization()->associate($organization)->save();

    app(MatchProspectToOrganization::class)($staleProspect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
});

it('does not affiliate a prospect with a soft-deleted organization', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.edu']], 'deleted_at' => now()]);
    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@acme.edu']);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBeNull();
});

it('does not affiliate a prospect when no email address matches', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@unmatched.test']);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBeNull();
});

it('affiliates a prospect that has been converted to a student', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $prospect = Prospect::factory()->for(Student::factory(), 'student')->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@acme.edu']);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBe($organization->getKey());
});
