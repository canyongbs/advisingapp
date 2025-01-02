<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Interaction\Database\Seeders;

use AdvisingApp\Interaction\Models\InteractionDriver;
use Illuminate\Database\Seeder;

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
