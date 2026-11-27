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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Task\Filament\Tables\AssignableTaskUsersTable;
use App\Models\Authenticatable;
use App\Models\User;

$createLicensedAdmin = function (): User {
    $admin = User::factory()->licensed(LicenseType::cases())->create();

    $admin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    return $admin;
};

it('only includes users with the license', function (): void {
    $licensedUser = User::factory()->licensed([LicenseType::RetentionCrm])->create();
    $otherLicenseUser = User::factory()->licensed([LicenseType::RecruitmentCrm])->create();
    $unlicensedUser = User::factory()->create();

    $userIds = AssignableTaskUsersTable::scope(User::query(), LicenseType::RetentionCrm)->pluck('id');

    expect($userIds)->toContain($licensedUser->getKey())
        ->not->toContain($otherLicenseUser->getKey())
        ->not->toContain($unlicensedUser->getKey());
});

it('excludes admins unless they are always included', function () use ($createLicensedAdmin): void {
    $admin = $createLicensedAdmin();
    $includedAdmin = $createLicensedAdmin();

    $userIds = AssignableTaskUsersTable::scope(User::query(), LicenseType::RetentionCrm, [$includedAdmin->getKey(), null])->pluck('id');

    expect($userIds)->not->toContain($admin->getKey())
        ->toContain($includedAdmin->getKey());
});

it('does not include always included users without the license', function (): void {
    $unlicensedUser = User::factory()->create();

    $userIds = AssignableTaskUsersTable::scope(User::query(), LicenseType::RetentionCrm, [$unlicensedUser->getKey()])->pluck('id');

    expect($userIds)->not->toContain($unlicensedUser->getKey());
});

it('includes admins when admins are not filtered from selection', function () use ($createLicensedAdmin): void {
    config(['app.filter_admins_from_selection' => false]);

    $admin = $createLicensedAdmin();

    $userIds = AssignableTaskUsersTable::scope(User::query(), LicenseType::RetentionCrm)->pluck('id');

    expect($userIds)->toContain($admin->getKey());
});

it('still requires the license when admins are not filtered from selection', function (): void {
    config(['app.filter_admins_from_selection' => false]);

    $unlicensedAdmin = User::factory()->create();
    $unlicensedAdmin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $userIds = AssignableTaskUsersTable::scope(User::query(), LicenseType::RetentionCrm)->pluck('id');

    expect($userIds)->not->toContain($unlicensedAdmin->getKey());
});
