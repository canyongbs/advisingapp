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

use function Pest\Laravel\seed;

use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\MaintenanceActivity;
use AdvisingApp\InventoryManagement\Database\Seeders\AssetStatusSeeder;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

beforeEach(function () {
    seed(AssetStatusSeeder::class);
});

it('will update an asset to an unavailable classification when an in progress maintenance activity is created', function () {
    // Given that we have an asset that is currently available
    $asset = Asset::factory()->available()->create();

    expect($asset->status->classification)->toBe(SystemAssetStatusClassification::Available);

    // And an in progress maintenance activity is created for the asset
    MaintenanceActivity::factory()
        ->inProgress()
        ->create(['asset_id' => $asset->id]);

    // The asset status should have been updated to an unavailable classification
    $asset->refresh();

    expect($asset->status->classification)->toBe(SystemAssetStatusClassification::Unavailable);
    expect($asset->status->name)->toBe('Under Maintenance');
});

it('will update an asset to an available classification when a maintenance activity is completed', function () {
    // Given that we have an asset that is currently unavailable
    $asset = Asset::factory()->unavailable()->create();

    expect($asset->status->classification)->toBe(SystemAssetStatusClassification::Unavailable);

    // And a maintenance activity is completed for the asset
    MaintenanceActivity::factory()
        ->completed()
        ->create(['asset_id' => $asset->id]);

    // The asset status should have been updated to an available classification
    $asset->refresh();

    expect($asset->status->classification)->toBe(SystemAssetStatusClassification::Available);
});
