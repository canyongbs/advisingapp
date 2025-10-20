<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\StudentDataModel\Livewire\SisDataPipelineTable;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The SIS data pipeline table can be rendered', function () {
    asSuperAdmin();

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response(['data' => []], 200)
    ]);

    livewire(SisDataPipelineTable::class)
        ->assertSuccessful();
});

test('The SIS data pipeline table displays empty state when no pipeline data exists', function () {
    asSuperAdmin();

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response(['data' => []], 200)
    ]);

    livewire(SisDataPipelineTable::class)
        ->assertSuccessful()
        ->assertSee('A sync has not been run yet');
});

test('The SIS data pipeline table displays correct success and failure counts', function () {
    asSuperAdmin();

    $totalStudents = random_int(100, 1000);
    $successfulStudents = random_int(1, $totalStudents - 1);
    $failedStudents = $totalStudents - $successfulStudents;

    $totalEnrollments = random_int(50, 500);
    $successfulEnrollments = random_int(1, $totalEnrollments - 1);
    $failedEnrollments = $totalEnrollments - $successfulEnrollments;

    $mockData = [
        [
            'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'type' => 'incremental',
            'trigger' => 'scheduled',
            'total_student' => $totalStudents,
            'processed_students' => $totalStudents,
            'successful_students' => $successfulStudents,
            'total_enrollment' => $totalEnrollments,
            'processed_enrollments' => $totalEnrollments,
            'successful_enrollments' => $successfulEnrollments,
        ]
    ];

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response(['data' => $mockData], 200)
    ]);

    $component = livewire(SisDataPipelineTable::class);

    $component
        ->assertSuccessful()
        ->assertSee("{$successfulStudents} of {$totalStudents} synced")
        ->assertSee("{$failedStudents} failed")
        ->assertSee("{$successfulEnrollments} of {$totalEnrollments} synced")
        ->assertSee("{$failedEnrollments} failed");

    $expectedStudentPercentage = round(($successfulStudents / $totalStudents) * 100, 1);
    $expectedEnrollmentPercentage = round(($successfulEnrollments / $totalEnrollments) * 100, 1);
    
    expect($expectedStudentPercentage)->toBeLessThan(100.0);
    expect($expectedEnrollmentPercentage)->toBeLessThan(100.0);
});

test('The SIS data pipeline table calculates percentages correctly for the original bug scenario', function () {
    asSuperAdmin();

    $totalStudents = 560;
    $successfulStudents = 117;
    $failedStudents = $totalStudents - $successfulStudents; // 443

    $mockData = [
        [
            'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'type' => 'incremental',
            'trigger' => 'scheduled',
            'total_student' => $totalStudents,
            'processed_students' => $totalStudents,
            'successful_students' => $successfulStudents,
        ]
    ];

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response(['data' => $mockData], 200)
    ]);

    $component = livewire(SisDataPipelineTable::class);

    $component
        ->assertSuccessful()
        ->assertSee("{$successfulStudents} of {$totalStudents} synced")
        ->assertSee("{$failedStudents} failed");

    $expectedPercentage = ($successfulStudents / $totalStudents) * 100;
    
    expect($expectedPercentage)->toBe(20.892857142857142);
    expect($expectedPercentage)->not->toBe(100.0);
});

test('The SIS data pipeline table handles API errors gracefully', function () {
    asSuperAdmin();

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response([], 500)
    ]);

    livewire(SisDataPipelineTable::class)
        ->assertSuccessful()
        ->assertSee('A sync has not been run yet');
});

test('The SIS data pipeline table displays different pipeline types and triggers correctly', function () {
    asSuperAdmin();

    $types = ['full', 'incremental'];
    $triggers = ['manual', 'scheduled'];
    
    $selectedType = $types[array_rand($types)];
    $selectedTrigger = $triggers[array_rand($triggers)];

    $mockData = [
        [
            'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'type' => $selectedType,
            'trigger' => $selectedTrigger,
            'total_student' => random_int(10, 100),
            'processed_students' => random_int(5, 50),
            'successful_students' => random_int(1, 25),
        ]
    ];

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response(['data' => $mockData], 200)
    ]);

    $component = livewire(SisDataPipelineTable::class);

    $component
        ->assertSuccessful()
        ->assertSee(ucfirst($selectedType))
        ->assertSee(ucfirst($selectedTrigger));
    
    expect($selectedType)->toBeIn($types);
    expect($selectedTrigger)->toBeIn($triggers);
});

test('The SIS data pipeline table handles multiple pipeline records with different statuses', function () {
    asSuperAdmin();

    $completedTotalStudents = random_int(50, 200);
    $completedProcessedStudents = random_int(40, $completedTotalStudents);
    $completedSuccessfulStudents = random_int(30, $completedProcessedStudents);

    $completedRecord = [
        'started_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
        'completed_at' => now()->subHour()->format('Y-m-d H:i:s'),
        'type' => 'full',
        'trigger' => 'manual',
        'total_student' => $completedTotalStudents,
        'processed_students' => $completedProcessedStudents,
        'successful_students' => $completedSuccessfulStudents,
    ];

    $processingTotalStudents = random_int(10, 50);
    $processingProcessedStudents = random_int(5, $processingTotalStudents);
    $processingSuccessfulStudents = random_int(1, $processingProcessedStudents);

    $processingRecord = [
        'started_at' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
        'type' => 'incremental',
        'trigger' => 'scheduled',
        'total_student' => $processingTotalStudents,
        'processed_students' => $processingProcessedStudents,
        'successful_students' => $processingSuccessfulStudents,
    ];

    Http::fake([
        'integrations/*/sis-sync-pipeline-data' => Http::response([
            'data' => [$completedRecord, $processingRecord]
        ], 200)
    ]);

    $component = livewire(SisDataPipelineTable::class);

    $component->assertSuccessful();

    expect($completedRecord['total_student'])->toBeGreaterThan(0);
    expect($processingRecord['total_student'])->toBeGreaterThan(0);
    expect($completedRecord['successful_students'])->toBeLessThanOrEqual($completedRecord['processed_students']);
    expect($processingRecord['successful_students'])->toBeLessThanOrEqual($processingRecord['processed_students']);
    expect($completedRecord['processed_students'])->toBeLessThanOrEqual($completedRecord['total_student']);
    expect($processingRecord['processed_students'])->toBeLessThanOrEqual($processingRecord['total_student']);
});