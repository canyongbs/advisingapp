<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Console\Commands\SyncRolesAndPermissions;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->run(SyncRolesAndPermissions::class);

        $this->call([
            UsersTableSeeder::class,
        ]);
    }
}
