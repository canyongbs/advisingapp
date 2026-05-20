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

use AdvisingApp\Engagement\Filament\Pages\SentItems;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\StudentDataModel\Enums\EmailHealthStatus;
use AdvisingApp\StudentDataModel\Enums\PhoneHealthStatus;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('displays the type column with channel icon, email address, and healthy status for email engagements', function () {
    asSuperAdmin();

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'student@university.edu']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertTableColumnExists('channel')
        ->assertCanSeeTableRecords([$engagement])
        ->assertSeeHtml('student@university.edu')
        ->assertSeeHtml(EmailHealthStatus::Healthy->getColorClasses())
        ->assertSeeHtml(EmailHealthStatus::Healthy->getTooltipText());
});

it('displays the type column with channel icon, phone number, and healthy status for sms engagements', function () {
    asSuperAdmin();

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551234567']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$engagement])
        ->assertSeeHtml('+15551234567')
        ->assertSeeHtml(PhoneHealthStatus::Healthy->getColorClasses())
        ->assertSeeHtml(PhoneHealthStatus::Healthy->getTooltipText());
});

it('displays the bounced status icon for a bounced email address', function () {
    asSuperAdmin();

    $email = 'bounced@example.com';
    BouncedEmailAddress::factory()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::Bounced->getColorClasses())
        ->assertSeeHtml(EmailHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for an opted-out email address', function () {
    asSuperAdmin();

    $email = 'optedout@example.com';
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $email]);

    $engagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $email]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertSeeHtml($email)
        ->assertSeeHtml(EmailHealthStatus::OptedOut->getColorClasses())
        ->assertSeeHtml(EmailHealthStatus::OptedOut->getTooltipText());
});

it('displays the bounced status icon for a bounced phone number', function () {
    asSuperAdmin();

    $phone = '+15552220000';
    BouncedPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::Bounced->getColorClasses())
        ->assertSeeHtml(PhoneHealthStatus::Bounced->getTooltipText());
});

it('displays the opted out status icon for an opted-out phone number', function () {
    asSuperAdmin();

    $phone = '+15553330000';
    SmsOptOutPhoneNumber::factory()->create(['number' => $phone]);

    $engagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $phone]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->forStudent()
        ->create(['dispatched_at' => now()]);

    livewire(SentItems::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertSeeHtml($phone)
        ->assertSeeHtml(PhoneHealthStatus::OptedOut->getColorClasses())
        ->assertSeeHtml(PhoneHealthStatus::OptedOut->getTooltipText());
});

it('resolves recipient route from latest message or falls back to recipient_route', function () {
    $emailEngagement = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'test@example.com']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create();

    expect($emailEngagement->getRecipientRoute())->toBe('test@example.com');

    $smsEngagement = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551234567']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create();

    expect($smsEngagement->getRecipientRoute())->toBe('+15551234567');

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

    $nullRoute = Engagement::factory()
        ->email()
        ->deliverNow()
        ->create(['recipient_route' => null]);

    expect($nullRoute->getRecipientRoute())->toBeNull();
});

it('resolves recipient route health status correctly', function () {
    $healthyEmail = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => 'healthy@example.com']),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create();

    expect($healthyEmail->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Healthy);

    $bouncedAddress = 'bounced@example.com';
    BouncedEmailAddress::factory()->create(['address' => $bouncedAddress]);

    $bouncedEmail = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $bouncedAddress]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create();

    expect($bouncedEmail->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::Bounced);

    $optedOutAddress = 'optedout@example.com';
    EmailAddressOptInOptOut::factory()->optedOut()->create(['address' => $optedOutAddress]);

    $optedOutEmail = Engagement::factory()
        ->has(
            EmailMessage::factory()->state(['recipient_address' => $optedOutAddress]),
            'latestEmailMessage'
        )
        ->email()
        ->deliverNow()
        ->create();

    expect($optedOutEmail->getRecipientRouteHealthStatus())->toBe(EmailHealthStatus::OptedOut);

    $healthyPhone = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => '+15551110000']),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create();

    expect($healthyPhone->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Healthy);

    $bouncedNumber = '+15552220000';
    BouncedPhoneNumber::factory()->create(['number' => $bouncedNumber]);

    $bouncedPhone = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $bouncedNumber]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create();

    expect($bouncedPhone->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::Bounced);

    $optedOutNumber = '+15553330000';
    SmsOptOutPhoneNumber::factory()->create(['number' => $optedOutNumber]);

    $optedOutPhone = Engagement::factory()
        ->has(
            SmsMessage::factory()->state(['recipient_number' => $optedOutNumber]),
            'latestSmsMessage'
        )
        ->sms()
        ->deliverNow()
        ->create();

    expect($optedOutPhone->getRecipientRouteHealthStatus())->toBe(PhoneHealthStatus::OptedOut);

    $nullRoute = Engagement::factory()
        ->email()
        ->deliverNow()
        ->create(['recipient_route' => null]);

    expect($nullRoute->getRecipientRouteHealthStatus())->toBeNull();
});
