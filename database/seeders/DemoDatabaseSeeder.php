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

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Assist\Division\Database\Seeders\DivisionSeeder;
use Assist\Interaction\Database\Seeders\InteractionSeeder;
use Assist\Prospect\Database\Seeders\ProspectSourceSeeder;
use Assist\Prospect\Database\Seeders\ProspectStatusSeeder;
use Assist\Consent\Database\Seeders\ConsentAgreementSeeder;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use Assist\KnowledgeBase\Database\Seeders\KnowledgeBaseCategorySeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestTypeSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestStatusSeeder;
use Assist\ServiceManagement\Database\Seeders\ServiceRequestPrioritySeeder;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
        ]);
    }
}
