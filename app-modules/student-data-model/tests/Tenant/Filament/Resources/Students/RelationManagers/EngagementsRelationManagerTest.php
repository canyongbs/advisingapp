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

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Filters\SelectFilter;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// The Messages tab is powered by a single relation manager shared by the student and
// prospect profiles, so every case below runs against both owners.
dataset('messages tab owners', [
    'student' => [Student::class, ViewStudent::class],
    'prospect' => [Prospect::class, ViewProspect::class],
]);

function createMessagesTabOwner(string $ownerType): Student | Prospect
{
    return match ($ownerType) {
        Student::class => Student::factory()->create(),
        Prospect::class => Prospect::factory()->create(),
        default => throw new Exception("Unsupported messages tab owner type [{$ownerType}]."),
    };
}

function actingAsUserWithMessagesTabAccess(Student | Prospect $owner): void
{
    actingAs(user(
        licenses: $owner::getLicenseType(),
        permissions: [
            $owner instanceof Student ? 'student.view-any' : 'prospect.view-any',
            $owner instanceof Student ? 'student.*.view' : 'prospect.*.view',
            'engagement.view-any',
            'engagement_response.view-any',
        ],
    ));
}

describe('filters', function () {
    it('only offers email and text as message types', function (string $ownerType, string $pageClass) {
        $owner = createMessagesTabOwner($ownerType);

        actingAsUserWithMessagesTabAccess($owner);

        livewire(EngagementsRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => $pageClass,
        ])
            ->assertTableFilterExists('type', fn (SelectFilter $filter): bool => $filter->getFormField()->getOptions() === [
                NotificationChannel::Email->value => 'Email',
                NotificationChannel::Sms->value => 'Text',
            ]);
    })->with('messages tab owners');

    it('filters messages by type', function (string $ownerType, string $pageClass) {
        $owner = createMessagesTabOwner($ownerType);

        actingAsUserWithMessagesTabAccess($owner);

        $emailEngagement = Engagement::factory()
            ->email()
            ->create([
                'recipient_id' => $owner->getKey(),
                'recipient_type' => $owner->getMorphClass(),
            ]);

        $smsEngagement = Engagement::factory()
            ->sms()
            ->create([
                'recipient_id' => $owner->getKey(),
                'recipient_type' => $owner->getMorphClass(),
            ]);

        $smsResponse = EngagementResponse::factory()
            ->sms()
            ->create([
                'sender_id' => $owner->getKey(),
                'sender_type' => $owner->getMorphClass(),
            ]);

        livewire(EngagementsRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => $pageClass,
        ])
            ->filterTable('type', NotificationChannel::Email->value)
            ->assertCanSeeTableRecords([$emailEngagement->timelineRecord])
            ->assertCanNotSeeTableRecords([$smsEngagement->timelineRecord, $smsResponse->timelineRecord])
            ->filterTable('type', NotificationChannel::Sms->value)
            ->assertCanSeeTableRecords([$smsEngagement->timelineRecord, $smsResponse->timelineRecord])
            ->assertCanNotSeeTableRecords([$emailEngagement->timelineRecord])
            ->removeTableFilter('type')
            ->assertCanSeeTableRecords([
                $emailEngagement->timelineRecord,
                $smsEngagement->timelineRecord,
                $smsResponse->timelineRecord,
            ]);
    })->with('messages tab owners');

    it('filters messages by direction', function (string $ownerType, string $pageClass) {
        $owner = createMessagesTabOwner($ownerType);

        actingAsUserWithMessagesTabAccess($owner);

        $engagement = Engagement::factory()
            ->email()
            ->create([
                'recipient_id' => $owner->getKey(),
                'recipient_type' => $owner->getMorphClass(),
            ]);

        $response = EngagementResponse::factory()
            ->sms()
            ->create([
                'sender_id' => $owner->getKey(),
                'sender_type' => $owner->getMorphClass(),
            ]);

        livewire(EngagementsRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => $pageClass,
        ])
            ->filterTable('direction', Engagement::class)
            ->assertCanSeeTableRecords([$engagement->timelineRecord])
            ->assertCanNotSeeTableRecords([$response->timelineRecord])
            ->filterTable('direction', EngagementResponse::class)
            ->assertCanSeeTableRecords([$response->timelineRecord])
            ->assertCanNotSeeTableRecords([$engagement->timelineRecord])
            ->removeTableFilter('direction')
            ->assertCanSeeTableRecords([$engagement->timelineRecord, $response->timelineRecord]);
    })->with('messages tab owners');
});
