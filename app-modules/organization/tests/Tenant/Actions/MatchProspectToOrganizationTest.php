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

use AdvisingApp\Organization\Actions\MatchProspectToOrganization;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;

use function Tests\setOrganizationSupportEnabled;

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

it('does not affiliate a prospect when organization support is disabled', function (): void {
    setOrganizationSupportEnabled(false);

    Organization::factory()->create(['domains' => [['domain' => 'acme.edu']]]);
    $prospect = Prospect::factory()->create();
    $prospect->primaryEmailAddress->update(['address' => 'person@acme.edu']);

    app(MatchProspectToOrganization::class)($prospect);

    expect($prospect->refresh()->organization_id)->toBeNull();
});
