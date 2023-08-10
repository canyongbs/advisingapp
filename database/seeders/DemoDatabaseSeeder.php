<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Case\Database\Seeders\CaseItemSeeder;
use Assist\Case\Database\Seeders\CaseUpdateSeeder;
use Assist\Case\Database\Seeders\CaseItemTypeSeeder;
use Assist\Prospect\Database\Seeders\ProspectSeeder;
use Assist\Case\Database\Seeders\CaseItemStatusSeeder;
use Assist\Case\Database\Seeders\CaseItemPrioritySeeder;
use Assist\Prospect\Database\Seeders\ProspectSourceSeeder;
use Assist\Prospect\Database\Seeders\ProspectStatusSeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseItemSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseCategorySeeder;

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
            ProspectStatusSeeder::class,
            ProspectSourceSeeder::class,
            ProspectSeeder::class,
            KnowledgeBaseCategorySeeder::class,
            KnowledgeBaseQualitySeeder::class,
            KnowledgeBaseStatusSeeder::class,
            KnowledgeBaseItemSeeder::class,
        ]);
    }
}
