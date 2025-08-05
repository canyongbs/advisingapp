<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestone_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $statuses = [
            ['title' => 'Not Started', 'description' => 'The milestone has been defined but work hasn’t yet commenced.'],
            ['title' => 'Planned', 'description' => 'Resources and a schedule are in place; execution is scheduled to begin soon.'],
            ['title' => 'In Progress', 'description' => 'Work toward the milestone is actively underway.'],
            ['title' => 'At Risk', 'description' => 'Issues have been identified that jeopardize on-time or on-scope delivery, mitigation actions are required.'],
            ['title' => 'Delayed', 'description' => 'The milestone’s target date has slipped due to blockers or reprioritization.'],
            ['title' => 'On Hold', 'description' => 'Work is paused, often pending a decision, resource availability, or external dependency.'],
            ['title' => 'Completed', 'description' => 'All acceptance criteria have been met, and deliverables have been signed off.'],
            ['title' => 'Verified', 'description' => 'Outputs have been tested or reviewed, confirming that completion is accurate.'],
            ['title' => 'Closed', 'description' => 'The milestone has been formally closed in project records, and no further work or review is expected.'],
            ['title' => 'Cancelled', 'description' => 'The milestone has been officially removed from the plan (e.g., due to scope change).'],
        ];

        foreach ($statuses as $status) {
            ProjectMilestoneStatus::create($status);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestone_statuses');
    }
};
