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

use App\Models\User;
use Illuminate\Database\Seeder;
use Assist\Prospect\Models\Prospect;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\EngagementDeliverable;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::where('email', 'sampleadmin@advising.app')->first();

        // Data for super admin
        $this->seedSubscribersFor($superAdmin);
        $this->seedEngagementsFor($superAdmin);
    }

    protected function seedSubscribersFor(User $user): void
    {
        // Student subscriptions
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $student->sisid,
                    'subscribable_type' => resolve(Student::class)->getMorphClass(),
                ]);
            });

        // Prospect subscriptions
        Prospect::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Prospect $prospect) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $prospect->id,
                    'subscribable_type' => resolve(Prospect::class)->getMorphClass(),
                ]);
            });
    }

    protected function seedEngagementsFor(User $user): void
    {
        // Student Engagements
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(), 'engagementDeliverables')
                        ->for($student, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($student, 'sender')
                    ->create();
            });

        // Prospect Engagements
        Prospect::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Prospect $prospect) use ($user) {
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(), 'engagementDeliverables')
                        ->for($prospect, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($prospect, 'sender')
                    ->create();
            });
    }
}
