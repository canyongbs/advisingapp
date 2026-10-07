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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

it('redirects a legacy URL to its canonical tab', function (string $page, string $tab) {
    asSuperAdmin();
    $application = Application::factory()->create();

    get(ApplicationResource::getUrl($page, ['record' => $application]))
        ->assertRedirect(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => $tab]));
})->with([
    ['edit', 'edit'],
    ['manage-application-workflows', 'workflows'],
    ['manage-submissions', 'submissions'],
    ['manage-notifications', 'notifications'],
]);

it('preserves submission modal parameters through the legacy redirect', function () {
    asSuperAdmin();
    $application = Application::factory()->create();
    $parameters = ['record' => $application, 'tableAction' => 'view', 'tableActionRecord' => $application->submissions()->firstOrFail()->id];

    get(ApplicationResource::getUrl('manage-submissions', $parameters))
        ->assertRedirect(ApplicationResource::getUrl('view', [...$parameters, 'tab' => 'submissions']));
});

describe('authorization', function () {
    it('denies a legacy edit URL to a view-only user', function (string $page) {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);
        $application = Application::factory()->create();

        get(ApplicationResource::getUrl($page, ['record' => $application]))->assertForbidden();
    })->with(['edit', 'manage-notifications']);
});
