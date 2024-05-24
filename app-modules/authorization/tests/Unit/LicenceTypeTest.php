<?php

use App\Models\User;
use App\Settings\LicenseSettings;
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

    $this->assertEquals(1, $licenseType->getSeatsInUse());
    $this->assertEquals($totalSeats - 1, $licenseType->getAvailableSeats());
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
    $superAdmin->assignRole('authorization.super_admin');

    $superAdmin->grantLicense($licenseType);

    $this->assertEquals(0, $licenseType->getSeatsInUse());
    $this->assertEquals($totalSeats, $licenseType->getAvailableSeats());
})
    ->with([
        LicenseType::ConversationalAi,
        LicenseType::RetentionCrm,
        LicenseType::RecruitmentCrm,
    ]);
