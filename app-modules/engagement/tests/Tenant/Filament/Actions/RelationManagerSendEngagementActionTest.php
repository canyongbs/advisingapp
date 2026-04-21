<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementsRelationManager;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Queue;

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

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->mountTableAction('engage')
        ->setTableActionData([
            'channel' => NotificationChannel::Email->value,
            'recipient_route_id' => $student->primaryEmailAddress?->getKey(),
            'subject' => $subject,
            'body' => $body,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($student->engagements()->count())->toBe(1);
    expect($student->engagements()->first()->channel)->toEqual(NotificationChannel::Email);
    expect($student->engagements()->first()->subject)->toEqual($subject);
    expect($student->engagements()->first()->body)->toEqual($body);
});

it('can create an SMS Engagement properly', function () {
    Queue::fake();

    asSuperAdmin();

    $settings = app(TwilioSettings::class);
    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';
    $settings->save();

    /** @var Student $student */
    $student = Student::factory()->create();

    $faker = fake();

    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->mountTableAction('engage')
        ->setTableActionData([
            'channel' => NotificationChannel::Sms->value,
            'recipient_route_id' => $student->primaryPhoneNumber?->getKey(),
            'body' => $body,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($student->engagements()->count())->toBe(1);
    expect($student->engagements()->first()->channel)->toEqual(NotificationChannel::Sms);
    expect($student->engagements()->first()->body)->toEqual($body);
});

it('disables new action when student has no valid email or sms', function () {
    asSuperAdmin();

    /** @var Student $student */
    $student = Student::factory()->create();

    // Remove all emails and phones
    $student->emailAddresses()->delete();
    $student->primary_email_id = null;
    $student->phoneNumbers()->delete();
    $student->primary_phone_id = null;

    $student->save();
    $student->refresh();

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionDisabled('engage');
});

it('enables new action when student has valid email', function () {
    asSuperAdmin();

    /** @var Student $student */
    $student = Student::factory()->create();

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionEnabled('engage');
});

it('disables new action when student email is bounced and phone unavailable', function () {
    asSuperAdmin();

    /** @var Student $student */
    $student = Student::factory()->create();

    // Remove all non-primary emails so bouncing the primary covers all emails
    $primaryEmail = $student->primaryEmailAddress;
    $student->emailAddresses()->where('id', '!=', $primaryEmail->getKey())->delete();

    // Bounce the primary email
    BouncedEmailAddress::factory()->create([
        'address' => $primaryEmail->address,
    ]);

    // Remove all phones
    $student->phoneNumbers()->delete();
    $student->primary_phone_id = null;
    $student->save();
    $student->refresh();

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionDisabled('engage');
});

it('shows enabled action when bounced email but valid sms exists', function () {
    asSuperAdmin();

    $settings = app(TwilioSettings::class);
    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';
    $settings->save();

    /** @var Student $student */
    $student = Student::factory()->create();

    // Bounce the primary email
    $primaryEmail = $student->primaryEmailAddress;
    BouncedEmailAddress::factory()->create([
        'address' => $primaryEmail->address,
    ]);

    // Phone remains valid
    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionEnabled('engage');
});
