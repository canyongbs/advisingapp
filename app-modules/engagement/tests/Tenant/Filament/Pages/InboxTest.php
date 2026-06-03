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
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Filament\Pages\Inbox;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('is gated with proper access control', function () {})->todo();

it('displays the correct details', function () {})->todo();

it('can properly filter sender type', function () {
    asSuperAdmin();

    $prospectEngagementResponses = EngagementResponse::factory()->count(5)->create(['sender_type' => (new Prospect())->getMorphClass()]);
    $studentEngagementResponses = EngagementResponse::factory()->count(5)->create(['sender_type' => (new Student())->getMorphClass()]);

    livewire(Inbox::class)
        ->set('tableRecordsPerPage', 10)
        ->removeTableFilter('care_team')
        ->assertCanSeeTableRecords($prospectEngagementResponses->merge($studentEngagementResponses))
        ->filterTable('sender_type', 'student')
        ->assertCanSeeTableRecords($studentEngagementResponses)
        ->assertCanNotSeeTableRecords($prospectEngagementResponses)
        ->filterTable('sender_type', 'prospect')
        ->assertCanSeeTableRecords($prospectEngagementResponses)
        ->assertCanNotSeeTableRecords($studentEngagementResponses);
});

it('can properly filter engagement response type', function () {
    asSuperAdmin();

    $emailEngagementResponses = EngagementResponse::factory()->count(5)->create(['type' => EngagementResponseType::Email]);
    $smsEngagementResponses = EngagementResponse::factory()->count(5)->create(['type' => EngagementResponseType::Sms]);

    livewire(Inbox::class)
        ->set('tableRecordsPerPage', 10)
        ->removeTableFilter('care_team')
        ->assertCanSeeTableRecords($emailEngagementResponses->merge($smsEngagementResponses))
        ->filterTable('type', EngagementResponseType::Sms->value)
        ->assertCanSeeTableRecords($smsEngagementResponses)
        ->assertCanNotSeeTableRecords($emailEngagementResponses)
        ->filterTable('type', EngagementResponseType::Email->value)
        ->assertCanSeeTableRecords($emailEngagementResponses)
        ->assertCanNotSeeTableRecords($smsEngagementResponses);
});

it('loads the Care Team filter as active when the component first renders', function () {
    asSuperAdmin();

    livewire(Inbox::class)
        ->assertTableFilterExists('care_team')
        ->assertSet('tableFilters.care_team.isActive', true);
});

it('can properly filter by care team', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $student->careTeam()->sync([$user->getKey()]);

    $careTeamResponses = EngagementResponse::factory()->count(3)->create([
        'sender_type' => (new Student())->getMorphClass(),
        'sender_id' => $student->getKey(),
    ]);

    $otherStudent = Student::factory()->create();
    $otherResponses = EngagementResponse::factory()->count(3)->create([
        'sender_type' => (new Student())->getMorphClass(),
        'sender_id' => $otherStudent->getKey(),
    ]);

    livewire(Inbox::class)
        ->assertCanSeeTableRecords($careTeamResponses)
        ->assertCanNotSeeTableRecords($otherResponses);
});
