<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Case\Database\Seeders\CaseItemSeeder;
use Assist\Case\Database\Seeders\CaseUpdateSeeder;
use Assist\Case\Database\Seeders\CaseItemTypeSeeder;
use Assist\Case\Database\Seeders\CaseItemStatusSeeder;
use Assist\Case\Database\Seeders\CaseItemPrioritySeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call(SyncRolesAndPermissions::class);

        $this->call([
            UsersTableSeeder::class,
            CaseItemPrioritySeeder::class,
            CaseItemStatusSeeder::class,
            CaseItemTypeSeeder::class,
            CaseItemSeeder::class,
            CaseUpdateSeeder::class,
        ]);
    }
}
