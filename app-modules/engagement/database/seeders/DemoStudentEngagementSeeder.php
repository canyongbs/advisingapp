<?php

namespace Assist\Engagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\EngagementDeliverable;

class DemoStudentEngagementSeeder extends Seeder
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
    }
}
