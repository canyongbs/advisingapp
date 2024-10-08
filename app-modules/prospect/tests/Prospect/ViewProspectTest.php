<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Pest\Laravel\seed;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Database\Seeders\ProspectStatusSeeder;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\ConvertToStudent;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFiles;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectTasks;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEvents;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\DisassociateStudent;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectCareTeam;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectPrograms;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEngagement;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ProspectServiceManagement;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectInteractions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ProspectEngagementTimeline;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectSubscriptions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFormSubmissions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectApplicationSubmissions;

// TODO: Write ViewProspectSource page test
//test('The correct details are displayed on the ViewProspect page', function () {});

// Permission Tests

test('ViewProspect is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $prospect = Prospect::factory()->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('view', [
                'record' => $prospect,
            ])
        )->assertForbidden();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('view', [
                'record' => $prospect,
            ])
        )->assertSuccessful();
});

test('convert action visible when prospect is not converted to student', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $prospect = Prospect::factory()->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    livewire(ViewProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(ConvertToStudent::class)
        ->assertActionHidden(DisassociateStudent::class);
});

test('disassociate student action visible when prospect is converted to student', function () {
    $user = User::factory()->licensed([Prospect::getLicenseType(), Student::getLicenseType()])->create();

    $prospect = Prospect::factory()
        ->for(Student::factory(), 'student')
        ->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    livewire(ViewProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(DisassociateStudent::class)
        ->assertActionHidden(ConvertToStudent::class);
});

test('convert prospect to student', function () {
    $user = User::factory()->licensed([Prospect::getLicenseType(), Student::getLicenseType()])->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    seed([
        ProspectStatusSeeder::class,
    ]);

    $prospect = Prospect::factory()
        ->create();

    $student = Student::factory()
        ->create();

    livewire(ViewProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->callAction(
            ConvertToStudent::class,
            data: ['student_id' => $student->getKey()]
        )->assertSuccessful();

    $prospect->refresh();

    $status = ProspectStatus::query()
        ->where('classification', 'converted')
        ->where('name', 'Converted')
        ->where('is_system_protected', true)
        ->firstOrFail();

    expect($prospect)
        ->status->toEqual($status);

    expect($prospect->student)
        ->sisid->toBe($student->sisid)
        ->full_name->toBe($student->full_name)
        ->email->toBe($student->email);
});

test('disassociate student from prospect', function () {
    $user = User::factory()->licensed([Prospect::getLicenseType(), Student::getLicenseType()])->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    seed([
        ProspectStatusSeeder::class,
    ]);

    $prospect = Prospect::factory()
        ->for(Student::factory(), 'student')
        ->create();

    livewire(ViewProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->callAction(
            DisassociateStudent::class,
        )->assertSuccessful();

    $prospect->refresh();

    $status = ProspectStatus::query()
        ->where('classification', 'new')
        ->where('name', 'New')
        ->where('is_system_protected', true)
        ->firstOrFail();

    expect($prospect)
        ->status->toEqual($status);

    expect($prospect->student()->exists())->toBeFalse();
});

test('can see prospect converted to student badge on', function (string $pages) {
    $user = User::factory()->licensed([Prospect::getLicenseType(), Student::getLicenseType()])->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');
    $user->givePermissionTo('prospect.*.update');

    $user->givePermissionTo('student.view-any');

    $user->givePermissionTo('alert.view-any');

    $user->givePermissionTo('user.view-any');

    $user->givePermissionTo('engagement.view-any');

    $user->givePermissionTo('engagement_file.view-any');

    $user->givePermissionTo('interaction.view-any');

    $user->givePermissionTo('task.*.view');

    $user->givePermissionTo('task.view-any');

    $user->givePermissionTo('care_team.view-any');

    $user->givePermissionTo('service_request.view-any');

    $user->givePermissionTo('event_attendee.view-any');

    $user->givePermissionTo('basic_needs_program.view-any');

    $user->givePermissionTo('timeline.access');

    actingAs($user);

    $prospect = Prospect::factory()
        ->for(Student::factory(), 'student')
        ->create();

    livewire($pages, [
        'record' => $prospect->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="prospect_converted_to_student"');
})
    ->with([
        ViewProspect::class,
        ManageProspectAlerts::class,
        ManageProspectEngagement::class,
        ManageProspectFiles::class,
        ManageProspectFormSubmissions::class,
        ManageProspectApplicationSubmissions::class,
        ManageProspectInteractions::class,
        ManageProspectSubscriptions::class,
        ManageProspectTasks::class,
        ProspectEngagementTimeline::class,
        ManageProspectCareTeam::class,
        ProspectServiceManagement::class,
        ManageProspectEvents::class,
        ManageProspectPrograms::class,
    ]);
