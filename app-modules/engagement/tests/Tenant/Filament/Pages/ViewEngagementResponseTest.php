<?php

use AdvisingApp\Engagement\Filament\Pages\ViewEngagementResponse;
use AdvisingApp\Engagement\Models\EngagementResponse;
use App\Settings\LicenseSettings;

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('requires the unified inbox feature addon to access', function () {
    $settings = app(LicenseSettings::class);

    asSuperAdmin();

    $engagementResponse = EngagementResponse::factory()->create();

    $settings->data->addons->unifiedInbox = false;
    $settings->save();

    get(ViewEngagementResponse::getUrl([$engagementResponse]))->assertForbidden();
    
    $settings->data->addons->unifiedInbox = true;
    $settings->save();

    get(ViewEngagementResponse::getUrl([$engagementResponse]))->assertOk();
});