<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Filament\Pages\ProfileInformation;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated by proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->publicProfiles = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    livewire(ProfileInformation::class)
        ->assertSchemaComponentHidden('public-profile');

    $settings->data->addons->publicProfiles = true;
    $settings->save();

    livewire(ProfileInformation::class)
        ->assertSchemaComponentVisible('public-profile');
});