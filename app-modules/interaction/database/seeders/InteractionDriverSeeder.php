<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionDriver;

class InteractionDriverSeeder extends Seeder
{
    public function run(): void
    {
        InteractionDriver::factory()
            ->createMany(
                [
                    ['name' => 'N/A'],
                    ['name' => 'College Applicant to Matriculation'],
                    ['name' => 'College Inquiry to Applicant'],
                    ['name' => 'College Matriculation to Enroll'],
                    ['name' => 'College RFI'],
                    ['name' => 'District Inquiry to Applicant'],
                    ['name' => 'Dual Reengagement'],
                    ['name' => 'Early College Plan Update'],
                    ['name' => 'Early College Transition'],
                    ['name' => 'Enrollment Cancellation Outreach'],
                    ['name' => 'Enrollment Cancellation Recovery'],
                    ['name' => 'Financial Aid Awarded Not Enrolled'],
                    ['name' => 'Financial Aid Task List'],
                    ['name' => 'MIH Follow Up'],
                    ['name' => 'MIH Text'],
                    ['name' => 'Third Party SEM'],
                ]
            );
    }
}
