<?php

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
