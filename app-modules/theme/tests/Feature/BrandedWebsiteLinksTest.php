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
