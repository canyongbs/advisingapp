<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void{}
};
