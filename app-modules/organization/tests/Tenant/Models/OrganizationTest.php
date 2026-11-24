<?php

use AdvisingApp\Organization\Models\Organization;
use Illuminate\Database\UniqueConstraintViolationException;

it('does not allow two organizations with the same name in a different case', function (): void {
    Organization::factory()->create(['name' => 'ACME Inc']);

    Organization::factory()->create(['name' => 'acme inc']);
})->throws(UniqueConstraintViolationException::class);

it('allows reusing the name of a soft-deleted organization', function (): void {
    $deletedOrganization = Organization::factory()->create(['name' => 'ACME Inc']);
    $deletedOrganization->delete();

    $prospectSource = Organization::factory()->create(['name' => 'ACME Inc']);

    expect($prospectSource->name)->toBe('ACME Inc')
        ->and(Organization::query()->where('name', 'ACME Inc')->count())->toBe(1);
});
