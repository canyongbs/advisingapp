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

use AdvisingApp\Portal\Settings\PortalSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->resource_hub_portal_enabled = true;
    $settings->save();
});

it('authenticates a student via a resource hub portal bearer token', function () {
    $student = Student::factory()->create();

    $token = $student->createToken('resource-hub-portal-access-token', ['resource-hub-portal']);

    get(route('portals.user.auth-check'), [
        'Authorization' => "Bearer {$token->plainTextToken}",
    ])
        ->assertSuccessful()
        ->assertJsonFragment(['sisid' => $student->sisid]);
});

it('authenticates a prospect via a resource hub portal bearer token', function () {
    $prospect = Prospect::factory()->create();

    $token = $prospect->createToken('resource-hub-portal-access-token', ['resource-hub-portal']);

    get(route('portals.user.auth-check'), [
        'Authorization' => "Bearer {$token->plainTextToken}",
    ])
        ->assertSuccessful()
        ->assertJsonFragment(['id' => $prospect->getKey()]);
});

it('resolves the portal token even when a first-party session user is present', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $token = $student->createToken('resource-hub-portal-access-token', ['resource-hub-portal']);

    get(route('portals.user.auth-check'), [
        'Authorization' => "Bearer {$token->plainTextToken}",
    ])
        ->assertSuccessful()
        ->assertJsonFragment(['sisid' => $student->sisid]);
});

it('rejects a token without the `resource-hub-portal` ability', function () {
    $student = Student::factory()->create();

    $token = $student->createToken('resource-hub-portal-access-token', ['some-other-ability']);

    getJson(route('portals.user.auth-check'), [
        'Authorization' => "Bearer {$token->plainTextToken}",
    ])
        ->assertUnauthorized();
});

it('rejects a request without a token', function () {
    getJson(route('portals.user.auth-check'))
        ->assertUnauthorized();
});
