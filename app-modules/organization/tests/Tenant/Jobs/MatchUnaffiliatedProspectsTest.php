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

    dispatch_sync(new MatchUnaffiliatedProspects($organization));

    expect($matchingProspect->refresh()->organization_id)->toBe($organization->getKey())
        ->and($otherProspect->refresh()->organization_id)->toBeNull();
});

it('affiliates a prospect with the organization matching its primary email address when matching for another organization', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $primaryOrganization = Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@globex.edu']);
    ProspectEmailAddress::factory()->for($prospect)->create(['address' => 'person@acme.edu', 'order' => 2]);

    dispatch_sync(new MatchUnaffiliatedProspects($organization));

    expect($prospect->refresh()->organization_id)->toBe($primaryOrganization->getKey());
});

it('does not fall back to matching every organization when the given organization no longer exists', function (): void {
    $deletedOrganization = Organization::factory()->create(['domains' => [['domain' => 'acme.edu']], 'deleted_at' => now()]);
    Organization::factory()->create(['domains' => [['domain' => 'globex.edu']]]);

    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@globex.edu']);

    dispatch_sync(new MatchUnaffiliatedProspects($deletedOrganization));

    expect($prospect->refresh()->organization_id)->toBeNull();
});
