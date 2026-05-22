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

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AdvisingApp\Engagement\Exceptions\UnableToDetectTenantFromSesS3EmailPayload;
use AdvisingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AdvisingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use App\Actions\Paths\ModulePath;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\partialMock;

it('handles spam verdict failure properly', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_spam'));

    $file = UploadedFile::fake()->createWithContent('s3_email_spam', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email_spam');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);

        $mock
            ->shouldReceive('fail')
            ->once()
            ->withArgs(function (Throwable $exception) {
                $invadedException = invade($exception);

                return $exception instanceof SesS3InboundSpamOrVirusDetected
                    && $invadedException->spamVerdict === 'FAIL'
                    && $invadedException->virusVerdict === 'PASS';
            });
    });

    invade($mock)->emailFilePath = 's3_email_spam';

    $mock->handle();

    assertDatabaseCount(Student::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
});

it('handles virus verdict failure properly', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_virus'));

    $file = UploadedFile::fake()->createWithContent('s3_email_virus', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email_virus');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);

        $mock
            ->shouldReceive('fail')
            ->once()
            ->withArgs(function (Throwable $exception) {
                $invadedException = invade($exception);

                return $exception instanceof SesS3InboundSpamOrVirusDetected
                    && $invadedException->spamVerdict === 'PASS'
                    && $invadedException->virusVerdict === 'FAIL';
            });
    });

    invade($mock)->emailFilePath = 's3_email_virus';

    $mock->handle();

    assertDatabaseCount(Student::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
});

it('properly handles not finding a Student or Prospect match and creates an UnmatchedInboundCommunication', function () {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $mock->handle();

    assertDatabaseCount(Student::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
    assertDatabaseCount(UnmatchedInboundCommunication::class, 1);
    assertDatabaseHas(UnmatchedInboundCommunication::class, [
        'subject' => 'This is a test',
        'sender' => 'kevin.ullyott@canyongbs.com',
        'type' => EngagementResponseType::Email->value,
    ]);
    $filesystem->assertMissing('s3_email');
});

it('properly creates an EngagementResponse for an inbound email matching a Student', function () {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $student = Student::factory()->create();

    StudentEmailAddress::factory()
        ->for($student, 'student')
        ->create(['address' => 'kevin.ullyott@canyongbs.com']);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    assertDatabaseHas(EngagementResponse::class, [
        'subject' => 'This is a test',
        'sender_id' => $student->getKey(),
        'sender_type' => $student->getMorphClass(),
        'type' => EngagementResponseType::Email->value,
        'status' => EngagementResponseStatus::New->value,
        'raw' => file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email')),
    ]);

    $filesystem->assertMissing('s3_email');
});

it('properly creates an EngagementResponse for an inbound email matching a Prospect', function () {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $prospect = Prospect::factory()->create();

    ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'kevin.ullyott@canyongbs.com']);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    assertDatabaseHas(EngagementResponse::class, [
        'subject' => 'This is a test',
        'sender_id' => $prospect->getKey(),
        'sender_type' => $prospect->getMorphClass(),
        'type' => EngagementResponseType::Email->value,
        'status' => EngagementResponseStatus::New->value,
        'raw' => file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email')),
    ]);

    $filesystem->assertMissing('s3_email');
});

it('handles attachments properly for a Student', function () {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $student = Student::factory()->create();

    StudentEmailAddress::factory()
        ->for($student, 'student')
        ->create(['address' => 'kevin.ullyott@canyongbs.com']);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_with_attachments'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $engagementResponses = EngagementResponse::all();

    expect($engagementResponses)->toHaveCount(1);

    $engagementResponse = $engagementResponses->first();

    assert($engagementResponse instanceof EngagementResponse);

    $inlineAttachments = $engagementResponse->getMedia('inline_attachments');

    expect($engagementResponse->getMedia('attachments'))->toHaveCount(2)
        ->and($inlineAttachments)->toHaveCount(1)
        ->and($inlineAttachments->first()->getCustomProperty('cid'))->toBe('image001.png@01DBEF93.EE8A3EB0')
        ->and($engagementResponse->subject)->toBe('This is a test')
        ->and($engagementResponse->sender_id)->toBe($student->getKey())
        ->and($engagementResponse->sender_type)->toBe($student->getMorphClass())
        ->and($engagementResponse->type)->toBe(EngagementResponseType::Email)
        ->and($engagementResponse->status)->toBe(EngagementResponseStatus::New)
        ->and($engagementResponse->raw)->toBe(file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_with_attachments')));

    $filesystem->assertMissing('s3_email');
});

it('handles exceptions correctly in the failed method', function (?Exception $exception, string $expectedPath) {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    $filesystem->assertExists('s3_email');

    $job = new ProcessSesS3InboundEmail('s3_email');

    $job->failed($exception);

    $filesystem->assertExists("{$expectedPath}/s3_email");
})->with([
    'null exception' => [null, '/failed'],
    'general exception' => [new Exception('General error'), '/failed'],
    'spam exception' => [new SesS3InboundSpamOrVirusDetected('s3_email', 'FAIL', 'PASS'), '/spam-or-virus-detected'],
    'virus exception' => [new SesS3InboundSpamOrVirusDetected('s3_email', 'PASS', 'FAIL'), '/spam-or-virus-detected'],
    'unable to retrieve content exception' => [new UnableToRetrieveContentFromSesS3EmailPayload('s3_email'), '/failed'],
    'unable to detect tenant exception' => [new UnableToDetectTenantFromSesS3EmailPayload('s3_email'), '/failed'],
]);
