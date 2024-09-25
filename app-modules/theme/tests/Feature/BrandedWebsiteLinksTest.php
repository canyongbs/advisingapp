<?php

namespace AdvisingApp\Theme\Tests\Feature;

use App\Http\Middleware\CheckOlympusKey;
use Illuminate\Support\Str;


test('Branded theme api test', function () {
    $data = [
        'is_support_url_enabled' => true,
        'support_url' => 'https://partners.olympus.local/support',
        'is_recent_updates_url_enabled' => false,
        'recent_updates_url' => '',
        'is_custom_link_url_enabled' => false,
        'custom_link_label' => '',
        'custom_link_url' => '',
        'tenant_id' => Str::uuid(),
    ];

    $response = $this->withoutMiddleware(CheckOlympusKey::class)->post('https://advisingapp.local/landlord/api/branded-website-links', $data);

    $response->assertStatus(200);
    
    $response->assertJson([
        'message' => 'test successful',
    ]);
});
