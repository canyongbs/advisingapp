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
