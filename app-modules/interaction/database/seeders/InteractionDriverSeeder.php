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
                    ['name' => 'Add Class Self Service'],
                    ['name' => 'Admission Application'],
                    ['name' => 'Advising'],
                    ['name' => 'Application WS'],
                    ['name' => 'Attending Other System College'],
                    ['name' => 'Bachelor Degree'],
                    ['name' => 'Book Advance'],
                    ['name' => 'Campus Event'],
                    ['name' => 'Campus Tour'],
                    ['name' => 'College Fair Event'],
                    ['name' => 'Consortium Agreement'],
                    ['name' => 'Course Applicability'],
                    ['name' => 'Course Selection'],
                    ['name' => 'Child Care Grant'],
                    ['name' => 'Disbursement'],
                    ['name' => 'Drop or Withdrawal'],
                    ['name' => 'MFA Authentication Support'],
                    ['name' => 'Early College'],
                    ['name' => 'Enrolled Student'],
                    ['name' => 'Enrollment Cancellation'],
                    ['name' => 'Enrollment Steps'],
                    ['name' => 'Enrollment Verification'],
                    ['name' => 'ESL'],
                    ['name' => 'Financial Aid Status'],
                    ['name' => 'Financial Aid Verification'],
                    ['name' => 'FAFSA'],
                    ['name' => 'FAFSA Completion Event'],
                    ['name' => 'Fast Track Certificates'],
                    ['name' => 'Fast Track Certificates'],
                    ['name' => 'Free College'],
                    ['name' => 'GED'],
                    ['name' => 'Graduation'],
                    ['name' => 'HEERF'],
                    ['name' => 'HS Visit'],
                    ['name' => 'HSI EXCELlence'],
                    ['name' => 'ID Authentication'],
                    ['name' => 'Incoming Transcripts'],
                    ['name' => 'Information Session 1 on 1'],
                    ['name' => 'Loan'],
                    ['name' => 'Loan Entrance Counseling'],
                    ['name' => 'Master Promissory Note'],
                    ['name' => 'Military Benefits'],
                    ['name' => 'MOV Activity'],
                    ['name' => 'New Student Orientation'],
                    ['name' => 'No Longer Interested'],
                    ['name' => 'On Campus Outreach Recruitment Event'],
                    ['name' => 'Other or General Information'],
                    ['name' => 'Outgoing Transcripts'],
                    ['name' => 'Outreach Outcomes'],
                    ['name' => 'Password Reset'],
                    ['name' => 'Pell'],
                    ['name' => 'Placement'],
                    ['name' => 'Prerequisites'],
                    ['name' => 'Private Loans'],
                    ['name' => 'R2T4'],
                    ['name' => 'Reengagement'],
                    ['name' => 'Refund'],
                    ['name' => 'Residency'],
                    ['name' => 'SAP and Maximum Time Frame'],
                    ['name' => 'SBS or Cashier'],
                    ['name' => 'Scholarships'],
                    ['name' => 'SEM Outreach'],
                    ['name' => 'Special Circumstance'],
                    ['name' => 'Technical Support'],
                    ['name' => 'Term Activation'],
                    ['name' => 'To Do List/Checklist'],
                    ['name' => 'Transfer to College'],
                    ['name' => 'Tuition or Fees'],
                    ['name' => 'Basic Needs'],
                ]
            );
    }
}
