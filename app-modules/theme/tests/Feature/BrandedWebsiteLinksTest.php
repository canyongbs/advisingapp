<?php

namespace AdvisingApp\Theme\Tests\Feature;

use App\Models\Tenant;
use App\Http\Middleware\CheckOlympusKey;

use function Pest\Laravel\withoutMiddleware;

use AdvisingApp\Theme\Settings\ThemeSettings;
use Worksome\RequestFactories\RequestFactory;
use AdvisingApp\Theme\Tests\RequestFactories\BrandedWebsiteLinksRequestFactory;

test('Branded theme api test', function () {
    $tenant = Tenant::current();

    $data = BrandedWebsiteLinksRequestFactory::new()->create();

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

it('validates the inputs', function (RequestFactory $requestFactory, $errors) {
    $tenant = Tenant::current();

    $data = $requestFactory->create();

    $request = withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('landlord.api.brandedWebsiteLinks.update'),
            $data
        )
        ->assertSessionHasErrors($errors);
})->with(
    [
        'is_support_url_enabled boolean' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['is_support_url_enabled' => 'blah']),
            ['is_support_url_enabled'],
        ],
        'support_url string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['support_url' => 123]),
            ['support_url'],
        ],
        'support_url url' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['support_url' => 'blah']),
            ['support_url'],
        ],
        'is_recent_updates_url_enabled boolean' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['is_recent_updates_url_enabled' => 'blah']),
            ['is_recent_updates_url_enabled'],
        ],
        'recent_updates_url string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['recent_updates_url' => 123]),
            ['recent_updates_url'],
        ],
        'recent_updates_url url' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['recent_updates_url' => 'blah']),
            ['recent_updates_url'],
        ],
        'is_custom_link_url_enabled boolean' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['is_custom_link_url_enabled' => 'blah']),
            ['is_custom_link_url_enabled'],
        ],
        'custom_link_label string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['custom_link_label' => 123]),
            ['custom_link_label'],
        ],
        'custom_link_url string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['custom_link_url' => 123]),
            ['custom_link_url'],
        ],
        'custom_link_url url' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['custom_link_url' => 'blah']),
            ['custom_link_url'],
        ],
        'tenant_id string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['tenant_id' => 123]),
            ['tenant_id'],
        ],
        'tenant_id required and missing' => [
            BrandedWebsiteLinksRequestFactory::new()->without(['tenant_id']),
            ['tenant_id'],
        ],
        'tenant_id required and null' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['tenant_id' => null]),
            ['tenant_id'],
        ],
        'tenant_id required and empty string' => [
            BrandedWebsiteLinksRequestFactory::new()->state(['tenant_id' => '']),
            ['tenant_id'],
        ],
    ]
);
