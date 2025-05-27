<?php

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can create an Email Engagement properly', function () {
    Queue::fake();

    asSuperAdmin();

    /** @var Student $student */
    $student = Student::factory()->create();

    $faker = fake();

    $subject = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->sentence()]]]]];
    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->mountAction('engage')
        ->setActionData([
            'channel' => NotificationChannel::Email->value,
            'recipient_route_id' => $student->primaryEmailAddress?->getKey(),
            'subject' => $subject,
            'body' => $body,
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($student->engagements()->count())->toBe(1);
    expect($student->engagements()->first()->channel)->toEqual(NotificationChannel::Email);
    expect($student->engagements()->first()->subject)->toEqual($subject);
    expect($student->engagements()->first()->body)->toEqual($body);
});

it('can create an SMS Engagement properly', function () {
    Queue::fake();

    asSuperAdmin();

    /** @var Student $student */
    $student = Student::factory()->create();

    $faker = fake();

    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->mountAction('engage')
        ->setActionData([
            'channel' => NotificationChannel::Sms->value,
            'recipient_route_id' => $student->primaryPhoneNumber?->getKey(),
            'body' => $body,
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($student->engagements()->count())->toBe(1);
    expect($student->engagements()->first()->channel)->toEqual(NotificationChannel::Sms);
    expect($student->engagements()->first()->body)->toEqual($body);
});
