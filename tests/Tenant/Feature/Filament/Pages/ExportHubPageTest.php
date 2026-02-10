<?php

use App\Filament\Pages\ExportHubPage;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ExportHubPage::getUrl())->assertForbidden();

    $user->givePermissionTo('export_hub.view-any');

    get(ExportHubPage::getUrl())->assertSuccessful();
});