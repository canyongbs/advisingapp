<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Task\Database\Seeders\TaskSeeder;
use Assist\Prospect\Database\Seeders\ProspectSeeder;
use Assist\Case\Database\Seeders\ServiceRequestSeeder;
use Assist\Engagement\Database\Seeders\EngagementSeeder;
use Assist\Case\Database\Seeders\ServiceRequestTypeSeeder;
use Assist\Prospect\Database\Seeders\ProspectSourceSeeder;
use Assist\Prospect\Database\Seeders\ProspectStatusSeeder;
use Assist\Case\Database\Seeders\ServiceRequestStatusSeeder;
use Assist\Case\Database\Seeders\ServiceRequestUpdateSeeder;
use Assist\Case\Database\Seeders\ServiceRequestPrioritySeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseItemSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseCategorySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call(SyncRolesAndPermissions::class);

        $this->call([
            UsersTableSeeder::class,
            ServiceRequestPrioritySeeder::class,
            ServiceRequestStatusSeeder::class,
            ServiceRequestTypeSeeder::class,
            ServiceRequestSeeder::class,
            ServiceRequestUpdateSeeder::class,
            ProspectStatusSeeder::class,
            ProspectSourceSeeder::class,
            ProspectSeeder::class,
            KnowledgeBaseCategorySeeder::class,
            KnowledgeBaseQualitySeeder::class,
            KnowledgeBaseStatusSeeder::class,
            KnowledgeBaseItemSeeder::class,
            TaskSeeder::class,
            StudentSeeder::class,
            EngagementSeeder::class,
        ]);
    }
}
