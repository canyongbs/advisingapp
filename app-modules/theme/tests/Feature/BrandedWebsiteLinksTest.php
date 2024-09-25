<?php

namespace AdvisingApp\Theme\Tests\Feature;

use AdvisingApp\Theme\Settings\ThemeSettings;
use App\Http\Middleware\CheckOlympusKey;

use App\Models\Tenant;
use function Pest\Laravel\withoutMiddleware;


test('Branded theme api test', function () {
    $tenant = Tenant::current();

    $data = [
        'is_support_url_enabled' => fake()->boolean(),
        'support_url' => fake()->url(),
        'is_recent_updates_url_enabled' => fake()->boolean(),
        'recent_updates_url' => fake()->url(),
        'is_custom_link_url_enabled' => fake()->boolean(),
        'custom_link_label' => fake()->word(),
        'custom_link_url' => fake()->url(),
        'tenant_id' => $tenant->getKey(),
    ];

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('landlord.api.brandedWebsiteLinks.update'),
            $data
        )
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Theme updated successfully!',
        ]);

    $tenant->execute(function () use ($data) {
        $settings = app(ThemeSettings::class);

        expect($settings->is_support_url_enabled)->toEqual($data['is_support_url_enabled']);
        expect($settings->support_url)->toEqual($data['support_url']);
        expect($settings->is_recent_updates_url_enabled)->toEqual($data['is_recent_updates_url_enabled']);
        expect($settings->recent_updates_url)->toEqual($data['recent_updates_url']);
        expect($settings->is_custom_link_url_enabled)->toEqual($data['is_custom_link_url_enabled']);
        expect($settings->custom_link_label)->toEqual($data['custom_link_label']);
        expect($settings->custom_link_url)->toEqual($data['custom_link_url']);
    });
});
