<?php

use AdvisingApp\Engagement\Filament\Pages\Inbox;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('is gated with proper access control', function () {})->todo();

it('displays the correct details', function () {})->todo();

it('can properly filter sender type', function() {
    asSuperAdmin();

    $prospectEngagementResponses = EngagementResponse::factory()->count(5)->create(['sender_type' => (new Prospect())->getMorphClass()]);
    $studentEngagementResponses = EngagementResponse::factory()->count(5)->create(['sender_type' => (new Student())->getMorphClass()]);

    livewire(Inbox::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($prospectEngagementResponses->merge($studentEngagementResponses))
        ->filterTable('sender_type', 'student')
        ->assertCanSeeTableRecords($studentEngagementResponses)
        ->assertCanNotSeeTableRecords($prospectEngagementResponses)
        ->filterTable('sender_type', 'prospect')
        ->assertCanSeeTableRecords($prospectEngagementResponses)
        ->assertCanNotSeeTableRecords($studentEngagementResponses);
});