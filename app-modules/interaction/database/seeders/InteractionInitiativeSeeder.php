<?php

namespace AdvisingApp\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Interaction\Models\InteractionInitiative;

class InteractionInitiativeSeeder extends Seeder
{
    public function run(): void
    {
        InteractionInitiative::factory()
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
