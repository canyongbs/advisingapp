<?php

use AdvisingApp\Organization\Jobs\MatchUnaffiliatedProspects;
use AdvisingApp\Organization\Models\Organization;
use App\Features\OrganizationFeature;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();

    Queue::fake();
});

it('matches unaffiliated prospects when an organization is created with domains', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);

    Queue::assertPushed(
        MatchUnaffiliatedProspects::class,
        fn (MatchUnaffiliatedProspects $job): bool => $job->organizationId === $organization->getKey(),
    );
});

it('does not match unaffiliated prospects when an organization is created without domains', function (): void {
    Organization::factory()->create(['domains' => null]);

    Queue::assertNotPushed(MatchUnaffiliatedProspects::class);
});

it('matches unaffiliated prospects when the domains of an organization change', function (): void {
    $organization = Organization::factory()->createQuietly(['domains' => [['domain' => 'acme.edu']]]);

    $organization->update(['domains' => [['domain' => 'acme.edu'], ['domain' => 'acme.org']]]);

    Queue::assertPushed(
        MatchUnaffiliatedProspects::class,
        fn (MatchUnaffiliatedProspects $job): bool => $job->organizationId === $organization->getKey(),
    );
});

it('does not match unaffiliated prospects when an organization is updated without changing its domains', function (): void {
    $organization = Organization::factory()->createQuietly(['domains' => [['domain' => 'acme.edu']]]);

    $organization->update(['name' => 'Acme Holdings']);

    Queue::assertNotPushed(MatchUnaffiliatedProspects::class);
});

it('does not match unaffiliated prospects when the domains of an organization are removed', function (): void {
    $organization = Organization::factory()->createQuietly(['domains' => [['domain' => 'acme.edu']]]);

    $organization->update(['domains' => null]);

    Queue::assertNotPushed(MatchUnaffiliatedProspects::class);
});
