<?php

use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Jobs\EngagementEmailWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('executes email workflow step successfully', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'created_by_type' => User::class,
        'created_by_id' => $user->id,
    ]);

    $workflowRun = WorkflowRun::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'related_type' => Student::class,
        'related_id' => $student->getKey(),
    ]);

    $emailDetails = WorkflowEngagementEmailDetails::factory()->create([
        'channel' => NotificationChannel::Email,
        'subject' => [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Test Subject',
                        ],
                    ],
                ],
            ],
        ],
        'body' => [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Test email body content',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->create([
        'workflow_run_id' => $workflowRun->id,
        'details_type' => WorkflowEngagementEmailDetails::class,
        'details_id' => $emailDetails->id,
        'execute_at' => now(),
    ]);

    $job = new EngagementEmailWorkflowActionJob($workflowRunStep);
    $job->handle();

    Notification::assertSentTo(
        $student,
        EngagementNotification::class,
        function ($notification) {
            return $notification->engagement->channel === NotificationChannel::Email;
        }
    );

    $workflowRunStep->refresh();
    expect($workflowRunStep->succeeded_at)->not->toBeNull();
});

it('throws exception for non-email channel', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'created_by_type' => User::class,
        'created_by_id' => $user->id,
    ]);

    $workflowRun = WorkflowRun::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'related_type' => Student::class,
        'related_id' => $student->getKey(),
    ]);

    $emailDetails = WorkflowEngagementEmailDetails::factory()->create([
        'channel' => NotificationChannel::Sms,
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->create([
        'workflow_run_id' => $workflowRun->id,
        'details_type' => WorkflowEngagementEmailDetails::class,
        'details_id' => $emailDetails->id,
        'execute_at' => now(),
    ]);

    $job = new EngagementEmailWorkflowActionJob($workflowRunStep);

    expect(fn () => $job->handle())
        ->toThrow(Exception::class, 'The notification channel is not email.');
});
