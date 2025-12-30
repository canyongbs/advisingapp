<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\Role;
use App\Models\User;
use Illuminate\Database\Events\MigrationStarted;
use Illuminate\Support\Collection;
use Tests\Exceptions\StopMigration;
use Tests\LandlordTestCase;
use Tests\TenantMigrationTestCase;
use Tests\TenantTestCase;

uses(TenantTestCase::class)->in('../tests/Tenant', '../app-modules/*/tests/Tenant');
uses(LandlordTestCase::class)->in('../tests/Landlord', '../app-modules/*/tests/Landlord');
uses(TenantMigrationTestCase::class)->in('../tests/TenantMigrationTests.php');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function isolatedMigration(string $migrationName, callable $callback): void
{
    Event::listen(
        MigrationStarted::class,
        function (MigrationStarted $event) use ($migrationName) {
            if (preg_match('/[^\/]*(?=\.php)/', get_class($event->migration), $matches)) {
                if ($matches[0] === $migrationName) {
                    throw new StopMigration();
                }
            }
        }
    );

    $stopped = false;

    try {
        Artisan::call('migrate:fresh');
    } catch (StopMigration $exception) { // @phpstan-ignore catch.neverThrown
        $stopped = true;
    }

    expect($stopped)->toBeTrue();

    // There may be a better way to do this, to clear out the event listener we added specifically
    // But this works for now.
    Event::forget(MigrationStarted::class);

    // TODO: Find a way to split the the call back into a setup, then migrate, then be able to assert
    $callback();
}

/**
 *
 * @param array<LicenseType>|LicenseType|null $licenses
 * @param array<string>|string|null $roles
 * @param array<string>|string|null $permissions
 *
 */
function user(LicenseType | array | null $licenses = null, array | null | string $roles = null, array | null | string $permissions = null, string $guard = 'web'): User
{
    $user = User::factory()->create();

    collect($roles)
        ->whenNotEmpty(function (Collection $collection) use ($guard, $user) {
            $roles = Role::query()
                ->whereIn('name', $collection)
                ->where('guard_name', $guard)
                ->get();

            $user->roles()->sync($roles);
        });

    collect($permissions)
        ->each(fn ($permission) => $user->givePermissionTo($permission));

    collect($licenses)
        ->each(fn (LicenseType $licenseType) => $user->grantLicense($licenseType))
        ->whenNotEmpty(fn () => $user->refresh());

    return $user;
}
