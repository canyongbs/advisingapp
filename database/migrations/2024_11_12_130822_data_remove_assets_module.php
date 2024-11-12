<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    public function up(): void
    {
        Schema::dropIfExists('asset_check_outs');
        Schema::dropIfExists('asset_check_ins');
        Schema::dropIfExists('maintenance_activities');
        Schema::dropIfExists('maintenance_providers');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_locations');
        Schema::dropIfExists('asset_statuses');
        Schema::dropIfExists('asset_types');

        $roleQuery = DB::table('roles')
            ->whereIn('name', ['inventory-management.inventory_management']);

        $roleIds = $roleQuery->pluck('id')->toArray();

        if(count($roleIds)){
            DB::table('role_has_permissions')
            ->where('role_id', $roleIds)
            ->delete();
        }

        $roleQuery->delete();

        $permissionGroups = DB::table('permission_groups')
            ->whereIn('name', [
                'Asset',
                'Asset Check In',
                'Asset Check Out',
                'Asset Location',
                'Asset Status',
                'Asset Type',
                'Maintenance Activity',
                'Maintenance Provider',
            ]);

        DB::table('permissions')
            ->whereIn('group_id', $permissionGroups->pluck('id')->toArray())
            ->delete();

        $permissionGroups->delete();
    }

    public function down(): void {}
};
