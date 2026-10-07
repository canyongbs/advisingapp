<?php

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
