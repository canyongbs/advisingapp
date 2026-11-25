<?php

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Organization\Rules\UniqueOrganizationDomain;
use Illuminate\Support\Facades\Validator;

it('fails for a domain used by another organization', function (string $domain): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.com']]]);

    $validator = Validator::make(['domain' => $domain], ['domain' => [new UniqueOrganizationDomain()]]);

    expect($validator->passes())->toBeFalse()
        ->and($validator->errors()->first('domain'))->toBe('This domain is already in use and may not be used a second time.');
})->with([
    'same case' => ['acme.com'],
    'different case' => ['ACME.com'],
]);

it('passes for a domain not used by any organization', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.com']]]);

    $validator = Validator::make(['domain' => 'globex.com'], ['domain' => [new UniqueOrganizationDomain()]]);

    expect($validator->passes())->toBeTrue();
});

it('passes for a domain used by the ignored organization', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.com']]]);

    $validator = Validator::make(['domain' => 'acme.com'], ['domain' => [new UniqueOrganizationDomain($organization->getKey())]]);

    expect($validator->passes())->toBeTrue();
});

it('passes for a domain used by a soft-deleted organization', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'acme.com']], 'deleted_at' => now()]);

    $validator = Validator::make(['domain' => 'acme.com'], ['domain' => [new UniqueOrganizationDomain()]]);

    expect($validator->passes())->toBeTrue();
});
