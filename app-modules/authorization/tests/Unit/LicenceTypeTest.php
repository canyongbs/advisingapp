<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use App\Models\Authenticatable;
use App\Settings\LicenseSettings;

use function PHPUnit\Framework\assertEquals;

use AdvisingApp\Authorization\Enums\LicenseType;

test('Users are counted in the available seats', function (LicenseType $licenseType) {
    $licenseSettings = app(LicenseSettings::class);

    $totalSeats = rand(1, 200);

    $licenseSettings->data->limits->conversationalAiSeats = $totalSeats;
    $licenseSettings->data->limits->retentionCrmSeats = $totalSeats;
    $licenseSettings->data->limits->recruitmentCrmSeats = $totalSeats;

    $licenseSettings->save();

    $superAdmin = User::factory()->create();

    $superAdmin->grantLicense($licenseType);

    assertEquals(1, $licenseType->getSeatsInUse());
    assertEquals($totalSeats - 1, $licenseType->getAvailableSeats());
})
    ->with([
        LicenseType::ConversationalAi,
        LicenseType::RetentionCrm,
        LicenseType::RecruitmentCrm,
    ]);

test('Users with a Super Admin role are not counted in the available seats', function (LicenseType $licenseType) {
    $licenseSettings = app(LicenseSettings::class);

    $totalSeats = rand(1, 200);

    $licenseSettings->data->limits->conversationalAiSeats = $totalSeats;
    $licenseSettings->data->limits->retentionCrmSeats = $totalSeats;
    $licenseSettings->data->limits->recruitmentCrmSeats = $totalSeats;

    $licenseSettings->save();

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $superAdmin->grantLicense($licenseType);

    assertEquals(0, $licenseType->getSeatsInUse());
    assertEquals($totalSeats, $licenseType->getAvailableSeats());
})
    ->with([
        LicenseType::ConversationalAi,
        LicenseType::RetentionCrm,
        LicenseType::RecruitmentCrm,
    ]);
