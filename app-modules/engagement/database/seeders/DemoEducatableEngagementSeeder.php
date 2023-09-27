<?php

namespace Assist\Engagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\Prospect;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\EngagementDeliverable;

class DemoEducatableEngagementSeeder extends Seeder
{
    public function run(): void
    {
        $sampleStudentForDemo = Student::factory()->create([
            'first' => 'Demo',
            'last' => 'Student',
            'email' => 'demo@student.com',
        ]);

        EngagementResponse::factory()
            ->count(5)
            ->for($sampleStudentForDemo, 'sender')
            ->create();

        Engagement::factory()
            ->count(7)
            ->has(EngagementDeliverable::factory()->deliverySuccessful()->count(1), 'engagementDeliverables')
            ->for($sampleStudentForDemo, 'recipient')
            ->create();

        $sampleProspectForDemo = Prospect::factory()->create([
            'first_name' => 'Demo',
            'last_name' => 'Prospect',
            'email' => 'demo@prospect.com',
        ]);

        EngagementResponse::factory()
            ->count(5)
            ->for($sampleProspectForDemo, 'sender')
            ->create();

        Engagement::factory()
            ->count(7)
            ->has(EngagementDeliverable::factory()->deliverySuccessful()->count(1), 'engagementDeliverables')
            ->for($sampleProspectForDemo, 'recipient')
            ->create();
    }
}
