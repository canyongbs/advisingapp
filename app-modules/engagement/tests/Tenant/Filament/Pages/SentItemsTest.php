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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Pages\SentItems;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Enums\EmailHealthStatus;
use AdvisingApp\StudentDataModel\Enums\PhoneHealthStatus;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('displays the type column with channel icon, email address, and healthy status for email engagements', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => 'student@university.edu']);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'student@university.edu']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertTableColumnExists('channel')
        ->assertCanSeeTableRecords([$engagement])
        ->assertTableColumnStateSet('channel', 'student@university.edu', record: $engagement);
});

it('displays the type column with channel icon, phone number, and healthy status for sms engagements', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => '+15551234567']);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551234567']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$engagement])
        ->assertTableColumnStateSet('channel', '+15551234567', record: $engagement);
});

it('displays the bounced status icon for a bounced email address', function () {
    asSuperAdmin();

    $email = 'bounced@example.com';
    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);
    BouncedEmailAddress::factory()->create(['address' => $email]);

    Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for an opted-out email address', function () {
    asSuperAdmin();

    $email = 'optedout@example.com';
    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $email]);

    Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::OptedOut->getTooltipText());
});

it('displays the bounced status icon for a bounced phone number', function () {
    asSuperAdmin();

    $phone = '+15552220000';
    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => $phone]);
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for an opted-out phone number', function () {
    asSuperAdmin();

    $phone = '+15553330000';
    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => $phone]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $phone]);

    Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::OptedOut->getTooltipText());
});

it('resolves email recipient route from latest email message', function () {
    asSuperAdmin();

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'test@example.com']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create();

    expect($engagement->getRecipientRoute())->toBe('test@example.com');
});

it('resolves sms recipient route from latest sms message', function () {
    asSuperAdmin();

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551234567']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create();

    expect($engagement->getRecipientRoute())->toBe('+15551234567');
});

it('falls back to recipient_route attribute when no message relationship exists', function () {
    asSuperAdmin();

    $emailFallback = Engagement::factory()
        ->email()
        ->deliverNow()
        ->create(['recipient_route' => 'fallback@example.com']);

    expect($emailFallback->getRecipientRoute())->toBe('fallback@example.com');

    $smsFallback = Engagement::factory()
        ->sms()
        ->deliverNow()
        ->create(['recipient_route' => '+15559876543']);

    expect($smsFallback->getRecipientRoute())->toBe('+15559876543');
});

it('returns null recipient route when no message and recipient_route is null', function () {
    asSuperAdmin();

    $engagement = Engagement::factory()
        ->email()
        ->deliverNow()
        ->create(['recipient_route' => null]);

    expect($engagement->getRecipientRoute())->toBeNull();
});

it('returns healthy email health status when address is not bounced or opted out', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => 'healthy@example.com']);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'healthy@example.com']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Healthy);
});

it('returns bounced email health status for a bounced email address', function () {
    asSuperAdmin();

    $email = 'bounced@example.com';
    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);
    BouncedEmailAddress::factory()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Bounced);
});

it('returns opted out email health status for an opted-out email address', function () {
    asSuperAdmin();

    $email = 'optedout@example.com';
    $student = Student::factory()->create();
    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::OptedOut);
});

it('returns healthy phone health status when number is not bounced or opted out', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => '+15551110000']);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551110000']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Healthy);
});

it('returns bounced phone health status for a bounced phone number', function () {
    asSuperAdmin();

    $phone = '+15552220000';
    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => $phone]);
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Bounced);
});

it('returns opted out phone health status for an opted-out phone number', function () {
    asSuperAdmin();

    $phone = '+15553330000';
    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->canReceiveSms()->create(['sisid' => $student->sisid, 'number' => $phone]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::OptedOut);
});

it('returns null health status when recipient route is null', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $engagement = Engagement::factory()
        ->email()
        ->deliverNow()
        ->create([
            'recipient_route' => null,
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBeNull();
});

it('returns bounced email health status even when no StudentEmailAddress record exists', function () {
    asSuperAdmin();

    $email = 'no-record-bounced@example.com';
    $student = Student::factory()->create();
    BouncedEmailAddress::factory()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Bounced);
});

it('returns bounced phone health status even when no StudentPhoneNumber record exists', function () {
    asSuperAdmin();

    $phone = '+15554440000';
    $student = Student::factory()->create();
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $student->sisid,
            'recipient_type' => (new Student())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Bounced);
});

it('displays the type column with channel icon, email address, and healthy status for prospect email engagements', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => 'prospect@university.edu']);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'prospect@university.edu']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertTableColumnExists('channel')
        ->assertCanSeeTableRecords([$engagement])
        ->assertTableColumnStateSet('channel', 'prospect@university.edu', record: $engagement);
});

it('displays the type column with channel icon, phone number, and healthy status for prospect sms engagements', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => '+15557654321']);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15557654321']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$engagement])
        ->assertTableColumnStateSet('channel', '+15557654321', record: $engagement);
});

it('displays the bounced status icon for a prospect bounced email address', function () {
    asSuperAdmin();

    $email = 'prospect-bounced@example.com';
    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => $email]);
    BouncedEmailAddress::factory()->create(['address' => $email]);

    Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for a prospect opted-out email address', function () {
    asSuperAdmin();

    $email = 'prospect-optedout@example.com';
    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => $email]);
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $email]);

    Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::OptedOut->getTooltipText());
});

it('displays the bounced status icon for a prospect bounced phone number', function () {
    asSuperAdmin();

    $phone = '+15556660000';
    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => $phone]);
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for a prospect opted-out phone number', function () {
    asSuperAdmin();

    $phone = '+15557770000';
    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => $phone]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $phone]);

    Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'dispatched_at' => now(),
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::OptedOut->getTooltipText());
});

it('returns healthy email health status for a prospect when address is not bounced or opted out', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => 'prospect-healthy@example.com']);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'prospect-healthy@example.com']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Healthy);
});

it('returns bounced email health status for a prospect bounced email address', function () {
    asSuperAdmin();

    $email = 'prospect-bounced@example.com';
    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => $email]);
    BouncedEmailAddress::factory()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Bounced);
});

it('returns opted out email health status for a prospect opted-out email address', function () {
    asSuperAdmin();

    $email = 'prospect-optedout@example.com';
    $prospect = Prospect::factory()->create();
    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => $email]);
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::OptedOut);
});

it('returns healthy phone health status for a prospect when number is not bounced or opted out', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => '+15558880000']);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15558880000']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Healthy);
});

it('returns bounced phone health status for a prospect bounced phone number', function () {
    asSuperAdmin();

    $phone = '+15556660000';
    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => $phone]);
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Bounced);
});

it('returns opted out phone health status for a prospect opted-out phone number', function () {
    asSuperAdmin();

    $phone = '+15557770000';
    $prospect = Prospect::factory()->create();
    ProspectPhoneNumber::factory()->canReceiveSms()->create(['prospect_id' => $prospect->id, 'number' => $phone]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create([
            'recipient_id' => $prospect->id,
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);

    expect($engagement->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::OptedOut);
});

it('When the component loads, the Care Team filter is already active.', function () {
    asSuperAdmin();

    livewire(SentItems::class)
        ->assertTableFilterExists('care_team')
        ->assertSet('tableFilters.care_team.isActive', true);
});
it('defaults to the care team filter', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $student->careTeam()->sync([$user->getKey()]);

    $careTeamEngagements = Engagement::factory()->count(3)->create([
        'recipient_type' => (new Student())->getMorphClass(),
        'recipient_id' => $student->getKey(),
        'user_id' => $user->getKey(),
    ]);

    $otherStudent = Student::factory()->create();
    $otherEngagements = Engagement::factory()->count(3)->create([
        'recipient_type' => (new Student())->getMorphClass(),
        'recipient_id' => $otherStudent->getKey(),
        'user_id' => $user->getKey(),
    ]);

    livewire(SentItems::class)
        ->assertCanSeeTableRecords($careTeamEngagements)
        ->assertCanNotSeeTableRecords($otherEngagements);
});
