<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use AdvisingApp\Ai\Database\Seeders\PromptSeeder;
use AdvisingApp\Form\Database\Seeders\FormSeeder;
use AdvisingApp\Task\Database\Seeders\TaskSeeder;
use AdvisingApp\Team\Database\Seeders\TeamSeeder;
use AdvisingApp\Alert\Database\Seeders\AlertSeeder;
use AdvisingApp\Ai\Database\Seeders\PromptTypeSeeder;
use AdvisingApp\Alert\Database\Seeders\AlertStatusSeeder;
use AdvisingApp\Division\Database\Seeders\DivisionSeeder;
use AdvisingApp\Prospect\Database\Seeders\ProspectSeeder;
use AdvisingApp\Authorization\Console\Commands\SetupRoles;
use AdvisingApp\CaseManagement\Database\Seeders\CaseSeeder;
use AdvisingApp\MeetingCenter\Database\Seeders\EventSeeder;
use AdvisingApp\CaseManagement\Database\Seeders\CaseTypeSeeder;
use AdvisingApp\Interaction\Database\Seeders\InteractionSeeder;
use AdvisingApp\Prospect\Database\Seeders\ProspectSourceSeeder;
use AdvisingApp\Prospect\Database\Seeders\ProspectStatusSeeder;
use AdvisingApp\Consent\Database\Seeders\ConsentAgreementSeeder;
use AdvisingApp\CaseManagement\Database\Seeders\CaseStatusSeeder;
use AdvisingApp\CaseManagement\Database\Seeders\CaseUpdateSeeder;
use AdvisingApp\ResourceHub\Database\Seeders\ResourceHubStatusSeeder;
use AdvisingApp\ResourceHub\Database\Seeders\ResourceHubArticleSeeder;
use AdvisingApp\ResourceHub\Database\Seeders\ResourceHubQualitySeeder;
use AdvisingApp\ResourceHub\Database\Seeders\ResourceHubCategorySeeder;
use AdvisingApp\CaseManagement\Database\Seeders\ChangeRequestTypeSeeder;
use AdvisingApp\CaseManagement\Database\Seeders\ChangeRequestStatusSeeder;
use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reduce notifications sent during seeding
        Notification::fake();

        $currentTenant = Tenant::current();

        Artisan::call(
            command: SetupRoles::class,
            parameters: [
                '--tenant' => $currentTenant->id,
            ],
            outputBuffer: $this->command->getOutput(),
        );

        $this->call([
            SampleSuperAdminUserSeeder::class,
            LocalDevelopmentSeeder::class,
            DivisionSeeder::class,
            CaseStatusSeeder::class,
            CaseTypeSeeder::class,
            ProspectStatusSeeder::class,
            ProspectSourceSeeder::class,
            ResourceHubCategorySeeder::class,
            ResourceHubQualitySeeder::class,
            ResourceHubStatusSeeder::class,
            ...InteractionSeeder::metadataSeeders(),
            ConsentAgreementSeeder::class,
            PronounsSeeder::class,

            CaseSeeder::class,
            CaseUpdateSeeder::class,
            ProspectSeeder::class,
            ResourceHubArticleSeeder::class,
            TaskSeeder::class,
            FormSeeder::class,          
            AlertStatusSeeder::class,
            AlertSeeder::class,
            TeamSeeder::class,
            SuperAdminSeeder::class,
            TwilioStudentSeeder::class,
            ApplicationSubmissionStateSeeder::class,
            EventSeeder::class,

            // Change Request
            ChangeRequestTypeSeeder::class,
            ChangeRequestStatusSeeder::class,

            PromptTypeSeeder::class,
            PromptSeeder::class,
        ]);
    }
}
