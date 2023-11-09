<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Form\Database\Seeders\FormSeeder;
use Assist\Task\Database\Seeders\TaskSeeder;
use Assist\Team\Database\Seeders\TeamSeeder;
use Illuminate\Support\Facades\Notification;
use Assist\Alert\Database\Seeders\AlertSeeder;
use Assist\Division\Database\Seeders\DivisionSeeder;
use Assist\Prospect\Database\Seeders\ProspectSeeder;
use Assist\Engagement\Database\Seeders\EngagementSeeder;
use Assist\Interaction\Database\Seeders\InteractionSeeder;
use Assist\Prospect\Database\Seeders\ProspectSourceSeeder;
use Assist\Prospect\Database\Seeders\ProspectStatusSeeder;
use Assist\Consent\Database\Seeders\ConsentAgreementSeeder;
use Assist\Engagement\Database\Seeders\EngagementResponseSeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseItemSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use Assist\Engagement\Database\Seeders\DemoEducatableEngagementSeeder;
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
            SuperAdminProfileSeeder::class,
            UsersTableSeeder::class,
            DivisionSeeder::class,
            ServiceRequestPrioritySeeder::class,
            ServiceRequestStatusSeeder::class,
            ServiceRequestTypeSeeder::class,
            ProspectStatusSeeder::class,
            ProspectSourceSeeder::class,
            KnowledgeBaseCategorySeeder::class,
            KnowledgeBaseQualitySeeder::class,
            KnowledgeBaseStatusSeeder::class,
            ...InteractionSeeder::metadataSeeders(),
            ConsentAgreementSeeder::class,
            PronounsSeeder::class,

            ServiceRequestSeeder::class,
            ServiceRequestUpdateSeeder::class,
            ProspectSeeder::class,
            KnowledgeBaseItemSeeder::class,
            TaskSeeder::class,
            FormSeeder::class,
            AlertSeeder::class,
            TeamSeeder::class,
            EngagementSeeder::class,
            EngagementResponseSeeder::class,
            DemoEducatableEngagementSeeder::class,
            SuperAdminSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
