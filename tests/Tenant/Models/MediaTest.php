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

use AdvisingApp\Engagement\Models\EngagementFile;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$captureTemporaryUrlOptions = function (): ArrayObject {
    $captured = new ArrayObject();

    Storage::fake('s3');

    Storage::disk('s3')->buildTemporaryUrlsUsing(function (string $path, DateTimeInterface $expiration, array $options) use ($captured): string {
        $captured->exchangeArray($options);

        return 'https://s3.test/' . $path;
    });

    return $captured;
};

$createEngagementFileMedia = function (string $name): Media {
    return EngagementFile::factory()->create()
        ->addMedia(UploadedFile::fake()->image('upload.png'))
        ->usingName($name)
        ->usingFileName(Str::random(40) . '.png')
        ->toMediaCollection('file');
};

describe('getDownloadFilename', function (): void {
    it('combines the original name with the stored extension', function (): void {
        $media = new Media();
        $media->name = 'Quarterly Report';
        $media->file_name = Str::random(40) . '.docx';

        expect($media->getDownloadFilename())->toBe('Quarterly Report.docx');
    });

    it('falls back to the stored file name when the name is blank', function (): void {
        $media = new Media();
        $media->name = '';
        $media->file_name = 'xyz.pdf';

        expect($media->getDownloadFilename())->toBe('xyz.pdf');
    });

    it('uses the name alone when the stored file has no extension', function (): void {
        $media = new Media();
        $media->name = 'README';
        $media->file_name = 'abc';

        expect($media->getDownloadFilename())->toBe('README');
    });
});

describe('getTemporaryUrl', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
    it('adds an inline disposition with the original filename when none is given', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $media = $createEngagementFileMedia('Quarterly Report');

        $media->getTemporaryUrl(now()->addMinute());

        expect($captured['ResponseContentDisposition'] ?? null)->toBe('inline; filename="Quarterly Report.png"');
    });

    it('adds the original filename to a bare attachment disposition', function (string $disposition) use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $media = $createEngagementFileMedia('Quarterly Report');

        $media->getTemporaryUrl(now()->addMinute(), options: ['ResponseContentDisposition' => $disposition]);

        expect($captured['ResponseContentDisposition'] ?? null)->toBe('attachment; filename="Quarterly Report.png"');
    })->with([
        'lowercase' => 'attachment',
        'padded mixed case' => ' Attachment ',
    ]);

    it('passes a caller supplied disposition through unchanged', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $media = $createEngagementFileMedia('Quarterly Report');

        $media->getTemporaryUrl(now()->addMinute(), options: ['ResponseContentDisposition' => 'attachment; filename="custom.txt"']);

        expect($captured['ResponseContentDisposition'] ?? null)->toBe('attachment; filename="custom.txt"');
    });

    it('keeps other options intact', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $media = $createEngagementFileMedia('Quarterly Report');

        $media->getTemporaryUrl(now()->addMinute(), options: ['ResponseContentType' => 'text/plain']);

        expect($captured['ResponseContentType'] ?? null)->toBe('text/plain')
            ->and($captured['ResponseContentDisposition'] ?? null)->toBe('inline; filename="Quarterly Report.png"');
    });

    it('builds an ASCII safe disposition for a non-Latin-1 name', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $name = "Screenshot 7.48.39\u{202F}PM";
        $media = $createEngagementFileMedia($name);

        $media->getTemporaryUrl(now()->addMinute());

        $disposition = $captured['ResponseContentDisposition'] ?? '';

        expect(mb_check_encoding($disposition, 'ASCII'))->toBeTrue()
            ->and($disposition)->toContain("filename*=utf-8''")
            ->and(rawurldecode($disposition))->toContain("{$name}.png");
    });

    it('does not throw for a name containing slashes', function () use ($captureTemporaryUrlOptions, $createEngagementFileMedia): void {
        $captured = $captureTemporaryUrlOptions();
        $media = $createEngagementFileMedia('a/b\\c');

        expect($media->getTemporaryUrl(now()->addMinute()))->toBeString();

        $disposition = $captured['ResponseContentDisposition'] ?? '';

        expect(mb_check_encoding($disposition, 'ASCII'))->toBeTrue()
            ->and($disposition)->toStartWith('inline;')
            ->and(Str::after($disposition, 'filename='))->not->toContain('/')
            ->and(Str::after($disposition, 'filename='))->not->toContain('\\');
    });

    it('does not add a disposition for a conversion', function () use ($captureTemporaryUrlOptions): void {
        $captured = $captureTemporaryUrlOptions();

        $media = User::factory()->create()
            ->addMedia(UploadedFile::fake()->image('me.png'))
            ->usingName('Me')
            ->toMediaCollection('avatar');

        $media->getTemporaryUrl(now()->addMinute(), 'avatar-height-250px');

        expect($captured->getArrayCopy())->not->toHaveKey('ResponseContentDisposition');
    });
});

describe('attachmentContentDisposition', function (): void {
    it('builds an ASCII safe disposition for a file name that is not Latin-1 representable', function (): void {
        // macOS screenshots use a narrow no-break space (U+202F) before AM/PM, which S3 rejects in the header.
        $media = new Media();
        $media->file_name = "Screenshot-2026-09-08-at-7.48.39\u{202F}PM.jpg";

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ASCII'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain("filename*=utf-8''")
            ->and(rawurldecode($disposition))->toContain($media->file_name);
    });

    it('builds a simple disposition for an ASCII file name', function (): void {
        $media = new Media();
        $media->file_name = 'report.png';

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ASCII'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain('report.png')
            ->and($disposition)->not->toContain('filename*');
    });
});
