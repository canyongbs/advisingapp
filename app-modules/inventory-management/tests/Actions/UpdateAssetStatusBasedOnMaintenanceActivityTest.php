<?php

use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\MaintenanceActivity;
use AdvisingApp\InventoryManagement\Database\Seeders\AssetStatusSeeder;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

beforeEach(function () {
    $this->seed(AssetStatusSeeder::class);
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
