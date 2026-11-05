<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Database\Factories\CustomerAdvisorLinkFactory;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\PreviewCustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use App\Features\LlamaParseLinkParsingFeature;
use App\Settings\LicenseSettings;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

beforeEach(function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    asSuperAdmin();
});

$previewAdvisorWithLink = function (Closure $applyState): PreviewCustomerAdvisor {
    $advisor = CustomerAdvisor::factory()->create();

    $applyState(CustomerAdvisorLink::factory()->for($advisor, 'advisor'))->create();

    $page = Livewire::test(PreviewCustomerAdvisor::class, ['record' => $advisor->getRouteKey()])->instance();

    assert($page instanceof PreviewCustomerAdvisor);

    return $page;
};

it('shows the advisor as learning while a link without content is being parsed', function (Closure $applyState) use ($previewAdvisorWithLink): void {
    $page = $previewAdvisorWithLink($applyState);

    expect($page->isProcessing())->toBeTrue()
        ->and($page->isFailed())->toBeFalse();
})->with([
    'parse job running for over an hour' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingInProgress()->state([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(90),
        'updated_at' => now()->subMinutes(90),
        'parsing_started_at' => now()->subMinutes(90),
    ])],
    'waiting to be submitted' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(10),
    ])],
]);

it('shows the advisor as failed when a link without content could not be parsed', function (Closure $applyState) use ($previewAdvisorWithLink): void {
    $page = $previewAdvisorWithLink($applyState);

    expect($page->isFailed())->toBeTrue()
        ->and($page->isProcessing())->toBeFalse();
})->with([
    'parsing failed just now' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingFailed()->state([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(10),
    ])],
    'never parsed' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state([
        'parsing_results' => null,
        'created_at' => now()->subDays(2),
    ])],
]);

it('does not show the advisor as failed or learning while a link still has content', function (Closure $applyState) use ($previewAdvisorWithLink): void {
    $page = $previewAdvisorWithLink($applyState);

    expect($page->isFailed())->toBeFalse()
        ->and($page->isProcessing())->toBeFalse();
})->with([
    'refresh failed' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingFailed()->state([
        'parsing_results' => 'Existing parsing results',
    ])],
    'refresh in progress' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingInProgress()->state([
        'parsing_results' => 'Existing parsing results',
    ])],
]);

// TODO: Cleanup Task (llamaparse-link-parsing): remove this test along with the feature flag.
it('judges links by when they were last updated while LlamaParse link parsing is inactive', function () use ($previewAdvisorWithLink): void {
    LlamaParseLinkParsingFeature::deactivate();

    $page = $previewAdvisorWithLink(fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(10),
        'updated_at' => now()->subHours(2),
    ]));

    expect($page->isFailed())->toBeTrue()
        ->and($page->isProcessing())->toBeFalse();
});
