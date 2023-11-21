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
use Assist\Interaction\Models\InteractionCampaign;

class InteractionCampaignSeeder extends Seeder
{
    public function run(): void
    {
        InteractionCampaign::factory()
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
