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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Notifications\ApplicationSubmissionNotification;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

it('links the author name to their student record', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $application = Application::factory()->create();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    $body = (new ApplicationSubmissionNotification($application, $submission))->toDatabase(User::factory()->create())['body'];

    expect($body)->toContain(StudentResource::getUrl('view', ['record' => $student]));
});

describe('archiving', function () {
    // The student's page no longer resolves, so the notification names them without a link
    // rather than sending the reader to a page that does not exist.
    it('names an archived author without linking to them', function () {
        asSuperAdmin();

        $student = Student::factory()->create();
        $application = Application::factory()->create();
        $submission = ApplicationSubmission::factory()->create([
            'application_id' => $application->id,
            'author_type' => $student->getMorphClass(),
            'author_id' => $student->getKey(),
        ]);

        $student->archive();

        $body = (new ApplicationSubmissionNotification($application, $submission))->toDatabase(User::factory()->create())['body'];

        expect($body)->toContain($student->first)
            ->and($body)->not->toContain(StudentResource::getUrl('view', ['record' => $student]));
    });
});
