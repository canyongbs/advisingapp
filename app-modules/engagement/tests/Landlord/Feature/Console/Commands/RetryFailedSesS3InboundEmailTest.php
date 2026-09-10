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

use AdvisingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;

it('moves a failed inbound email back out of the failed folder and queues it for reprocessing from its original path', function () {
    Bus::fake();

    $filesystem = Storage::fake('s3-inbound-email');

    $filesystem->putFileAs('failed', UploadedFile::fake()->createWithContent('s3_email', 'raw'), 's3_email');

    $filesystem->assertExists('failed/s3_email');

    artisan('engagement:retry-failed-inbound-email', ['path' => 'failed/s3_email'])
        ->assertSuccessful();

    $filesystem->assertExists('s3_email');
    $filesystem->assertMissing('failed/s3_email');

    Bus::assertDispatched(
        ProcessSesS3InboundEmail::class,
        // @phpstan-ignore-next-line
        fn (ProcessSesS3InboundEmail $job): bool => invade($job)->emailFilePath === 's3_email',
    );
});

it('normalizes a full s3 object key including the disk root and a leading slash', function () {
    Bus::fake();

    config()->set('filesystems.disks.s3-inbound-email.root', 'some-root/inbound-email');

    $filesystem = Storage::fake('s3-inbound-email');

    $filesystem->putFileAs('failed', UploadedFile::fake()->createWithContent('s3_email', 'raw'), 's3_email');

    artisan('engagement:retry-failed-inbound-email', ['path' => 's3://some-bucket/some-root/inbound-email/failed/s3_email'])
        ->assertSuccessful();

    $filesystem->assertExists('s3_email');
    $filesystem->assertMissing('failed/s3_email');

    Bus::assertDispatched(
        ProcessSesS3InboundEmail::class,
        // @phpstan-ignore-next-line
        fn (ProcessSesS3InboundEmail $job): bool => invade($job)->emailFilePath === 's3_email',
    );
});

it('fails and dispatches nothing when the file does not exist', function () {
    Bus::fake();

    Storage::fake('s3-inbound-email');

    artisan('engagement:retry-failed-inbound-email', ['path' => 'failed/missing'])
        ->assertFailed();

    Bus::assertNothingDispatched();
});
