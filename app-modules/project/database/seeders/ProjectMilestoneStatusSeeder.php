<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Project\Database\Seeders;

use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use Illuminate\Database\Seeder;

class ProjectMilestoneStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Not Started', 'description' => 'The milestone has been defined but work hasn’t yet commenced.'],
            ['name' => 'Planned', 'description' => 'Resources and a schedule are in place; execution is scheduled to begin soon.'],
            ['name' => 'In Progress', 'description' => 'Work toward the milestone is actively underway.'],
            ['name' => 'At Risk', 'description' => 'Issues have been identified that jeopardize on-time or on-scope delivery, mitigation actions are required.'],
            ['name' => 'Delayed', 'description' => 'The milestone’s target date has slipped due to blockers or reprioritization.'],
            ['name' => 'On Hold', 'description' => 'Work is paused, often pending a decision, resource availability, or external dependency.'],
            ['name' => 'Completed', 'description' => 'All acceptance criteria have been met, and deliverables have been signed off.'],
            ['name' => 'Verified', 'description' => 'Outputs have been tested or reviewed, confirming that completion is accurate.'],
            ['name' => 'Closed', 'description' => 'The milestone has been formally closed in project records, and no further work or review is expected.'],
            ['name' => 'Cancelled', 'description' => 'The milestone has been officially removed from the plan (e.g., due to scope change).'],
        ];

        foreach ($statuses as $status) {
            ProjectMilestoneStatus::firstOrCreate(
                ['name' => $status['name']],
                ['description' => $status['description']]
            );
        }
    }
}
