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

use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can create a bulk SMS Engagement properly for students', function () {
    Queue::fake();

    asSuperAdmin();

    $students = Student::factory()->count(3)->create();

    $faker = fake();

    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ListStudents::class)
        ->mountTableBulkAction('send_text', $students->pluck('sisid')->toArray())
        ->setTableBulkActionData([
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

it('can create a bulk SMS Engagement properly for prospects', function () {
    Queue::fake();

    asSuperAdmin();

    $prospects = Prospect::factory()->count(3)->create();

    $faker = fake();

    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $faker->paragraph()]]]]];

    livewire(ListProspects::class)
        ->mountTableBulkAction('send_text', $prospects->pluck('id')->toArray())
        ->setTableBulkActionData([
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
