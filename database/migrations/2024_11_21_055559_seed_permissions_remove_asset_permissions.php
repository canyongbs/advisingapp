<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'asset.*.delete' => 'Asset',
        'asset.*.force-delete' => 'Asset',
        'asset.*.restore' => 'Asset',
        'asset.*.update' => 'Asset',
        'asset.*.view' => 'Asset',
        'asset.create' => 'Asset',
        'asset.view-any' => 'Asset',
        'asset_check_in.*.delete' => 'Asset Check In',
        'asset_check_in.*.force-delete' => 'Asset Check In',
        'asset_check_in.*.restore' => 'Asset Check In',
        'asset_check_in.*.update' => 'Asset Check In',
        'asset_check_in.*.view' => 'Asset Check In',
        'asset_check_in.create' => 'Asset Check In',
        'asset_check_in.view-any' => 'Asset Check In',
        'asset_check_out.*.delete' => 'Asset Check Out',
        'asset_check_out.*.force-delete' => 'Asset Check Out',
        'asset_check_out.*.restore' => 'Asset Check Out',
        'asset_check_out.*.update' => 'Asset Check Out',
        'asset_check_out.*.view' => 'Asset Check Out',
        'asset_check_out.create' => 'Asset Check Out',
        'asset_check_out.view-any' => 'Asset Check Out',
        'asset_location.*.delete' => 'Asset Location',
        'asset_location.*.force-delete' => 'Asset Location',
        'asset_location.*.restore' => 'Asset Location',
        'asset_location.*.update' => 'Asset Location',
        'asset_location.*.view' => 'Asset Location',
        'asset_location.create' => 'Asset Location',
        'asset_location.view-any' => 'Asset Location',
        'asset_status.*.delete' => 'Asset Status',
        'asset_status.*.force-delete' => 'Asset Status',
        'asset_status.*.restore' => 'Asset Status',
        'asset_status.*.update' => 'Asset Status',
        'asset_status.*.view' => 'Asset Status',
        'asset_status.create' => 'Asset Status',
        'asset_status.view-any' => 'Asset Status',
        'asset_type.*.delete' => 'Asset Type',
        'asset_type.*.force-delete' => 'Asset Type',
        'asset_type.*.restore' => 'Asset Type',
        'asset_type.*.update' => 'Asset Type',
        'asset_type.*.view' => 'Asset Type',
        'asset_type.create' => 'Asset Type',
        'asset_type.view-any' => 'Asset Type',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });

        DB::table('permission_groups')
            ->whereIn('name', [
                'Asset',
                'Asset Check In',
                'Asset Check Out',
                'Asset Location',
                'Asset Status',
                'Asset Type',
            ])
            ->delete();
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
