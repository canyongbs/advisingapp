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

use AdvisingApp\Application\Actions\CreateApplicationVersion;
use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Jobs\SendApplicationSubmissionAutoReplyEmail;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Notifications\ApplicationSubmissionAutoReplyNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);

    Queue::fake([SendApplicationSubmissionAutoReplyEmail::class]);
});

$richText = fn (array $content): array => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => $content]]];

it('renders the subject and body of the latest application version for the author', function () use ($richText) {
    $student = Student::factory()->create(['first' => 'Jamie']);
    $oldVersion = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $oldVersion->getKey(),
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    $newVersion = app(CreateApplicationVersion::class)->execute($oldVersion->refresh(), ['name' => $oldVersion->name]);
    $newVersion->emailAutoReply()->firstOrFail()->update([
        'is_enabled' => true,
        'subject' => $richText([
            ['type' => 'text', 'text' => 'Thanks '],
            ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient first name']],
        ]),
        'body' => $richText([
            ['type' => 'text', 'text' => 'We have received your application.'],
        ]),
    ]);

    $mail = (new ApplicationSubmissionAutoReplyNotification($submission))->toMail($student);

    expect($mail->subject)->toBe('Thanks Jamie')
        ->and($mail->viewData['content'])->toContain('We have received your application.');
});
