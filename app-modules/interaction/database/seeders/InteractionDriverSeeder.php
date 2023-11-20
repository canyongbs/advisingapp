<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
