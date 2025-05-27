<?php

use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can create an Email Engagement properly', function () {
    Queue::fake();

    asSuperAdmin();

    $students = Student::factory()->count(3)->create();

    $faker = fake();

    $subject = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->sentence()]]]]];
    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ListStudents::class)
        ->mountTableBulkAction('engage', $students->pluck('sisid')->toArray())
        ->setTableBulkActionData([
            'channel' => NotificationChannel::Email->value,
            'subject' => $subject,
            'body' => $body,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors();

    assertDatabaseCount(EngagementBatch::class, 1);

    $engagementBatch = EngagementBatch::first();

    expect($engagementBatch->channel)->toEqual(NotificationChannel::Email);
    expect($engagementBatch->subject)->toEqual($subject);
    expect($engagementBatch->body)->toEqual($body);
    Queue::assertPushed(CreateBatchedEngagement::class, 3);
});

it('can create an SMS Engagement properly', function () {
    Queue::fake();

    asSuperAdmin();

    $students = Student::factory()->count(3)->create();

    $faker = fake();

    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ListStudents::class)
        ->mountTableBulkAction('engage', $students->pluck('sisid')->toArray())
        ->setTableBulkActionData([
            'channel' => NotificationChannel::Sms->value,
            'body' => $body,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors();

    assertDatabaseCount(EngagementBatch::class, 1);

    $engagementBatch = EngagementBatch::first();

    expect($engagementBatch->channel)->toEqual(NotificationChannel::Sms);
    expect($engagementBatch->body)->toEqual($body);
    Queue::assertPushed(CreateBatchedEngagement::class, 3);
});
