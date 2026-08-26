<?php

use AdvisingApp\Campaign\Filament\Pages\ManageCampaignSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires all three permissions to access', function (array $permissions, bool $accessible) {
    $user = User::factory()->create();

    $user->givePermissionTo($permissions);

    actingAs($user);

    $response = get(ManageCampaignSettings::getUrl());

    $accessible ? $response->assertOk() : $response->assertForbidden();
})->with([
    'none' => [[], false],
    'view-any only' => [['settings.view-any'], false],
    'view only' => [['settings.*.view'], false],
    'update only' => [['settings.*.update'], false],
    'view-any + view' => [['settings.view-any', 'settings.*.view'], false],
    'view-any + update' => [['settings.view-any', 'settings.*.update'], false],
    'view + update' => [['settings.*.view', 'settings.*.update'], false],
    'view-any + view + update' => [['settings.view-any', 'settings.*.view', 'settings.*.update'], true],
]);
