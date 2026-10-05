<?php

use AdvisingApp\Engagement\Filament\Pages\ViewEngagement;
use AdvisingApp\Engagement\Models\Engagement;
use App\Settings\LicenseSettings;

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('requires the unified inbox feature addon to access', function () {
    $settings = app(LicenseSettings::class);

    asSuperAdmin();

    $engagement = Engagement::factory()->create();

    $settings->data->addons->unifiedInbox = false;
    $settings->save();

    get(ViewEngagement::getUrl([$engagement]))->assertForbidden();
    
    $settings->data->addons->unifiedInbox = true;
    $settings->save();

    get(ViewEngagement::getUrl([$engagement]))->assertOk();
});