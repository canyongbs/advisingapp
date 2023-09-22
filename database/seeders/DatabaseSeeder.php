<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Task\Database\Seeders\TaskSeeder;
use Illuminate\Support\Facades\Notification;
use Assist\Division\Database\Seeders\DivisionSeeder;
use Assist\Prospect\Database\Seeders\ProspectSeeder;
use Assist\Interaction\Database\Seeders\InteractionSeeder;
use Assist\Prospect\Database\Seeders\ProspectSourceSeeder;
use Assist\Prospect\Database\Seeders\ProspectStatusSeeder;
use Assist\Consent\Database\Seeders\ConsentAgreementSeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseItemSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseCategorySeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestTypeSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestStatusSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestUpdateSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestPrioritySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reduce notifications sent during seeding
        Notification::fake();

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
            DivisionSeeder::class,
            ...InteractionSeeder::metadataSeeders(),
            ConsentAgreementSeeder::class,
            InternalUsersSeeder::class,
        ]);
    }
}
