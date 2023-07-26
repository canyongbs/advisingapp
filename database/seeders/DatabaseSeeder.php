<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Case\Database\Seeders\CaseItemSeeder;
use App\Console\Commands\SyncRolesAndPermissions;
use Assist\Case\Database\Seeders\CaseItemStatusSeeder;
use Assist\Case\Database\Seeders\CaseItemPrioritySeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call(SyncRolesAndPermissions::class);

        $this->call([
            UsersTableSeeder::class,
            CaseItemPrioritySeeder::class,
            CaseItemStatusSeeder::class,
            CaseItemSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
