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

use AdvisingApp\Form\Filament\Blocks\EducatableUploadFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

use function Tests\asSuperAdmin;

/**
 * A macOS screenshot name: the space before "PM" is a narrow no-break space (U+202F),
 * which is not representable in ASCII and is what breaks the raw S3 download link.
 */
function nonLatin1FileName(): string
{
    return "Screenshot-2024-01-01-at-9.41.00\u{202F}PM.png";
}

/**
 * Create a form submission upload field backed by a real media record.
 *
 * @return array{0: FormField, 1: Media}
 */
function createSubmissionFieldWithMedia(string $fileName, string $collection = 'files'): array
{
    $form = Form::factory()->create();
    $field = FormField::factory()->create(['form_id' => $form->id]);
    $submission = FormSubmission::factory()->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $submission->fields()->attach($field, [
        'id' => (string) Str::orderedUuid(),
        'response' => json_encode([]),
    ]);

    $fieldWithPivot = $submission->fields()->firstOrFail();

    $media = $fieldWithPivot->pivot
        ->addMedia(UploadedFile::fake()->image('shot.png'))
        ->usingName('shot')
        ->usingFileName($fileName)
        ->toMediaCollection($collection);

    return [$fieldWithPivot, $media];
}

beforeEach(function () {
    Storage::fake('s3');
});

describe('SubmissionMediaDownloadController', function () {
    it('redirects to a temporary url whose content disposition is ASCII safe for a non-Latin-1 filename', function () {
        $captured = null;

        $disk = Storage::disk('s3');

        $disk->buildTemporaryUrlsUsing(function (string $path, DateTimeInterface $expiration, array $options) use (&$captured): string {
            $captured = $options['ResponseContentDisposition'] ?? null;

            return 'https://s3.test/' . $path;
        });

        asSuperAdmin();

        [, $media] = createSubmissionFieldWithMedia(nonLatin1FileName());

        $url = URL::temporarySignedRoute(
            'submission-media.download',
            now()->addDay(),
            ['media' => $media->getKey()],
        );

        get($url)->assertRedirect();

        expect($captured)->not->toBeNull()
            ->and(mb_check_encoding($captured, 'ASCII'))->toBeTrue()
            ->and($captured)->toStartWith('attachment;')
            ->and($captured)->toContain("filename*=utf-8''");
    });

    it('aborts unsigned requests', function () {
        asSuperAdmin();

        [, $media] = createSubmissionFieldWithMedia('report.png');

        get(route('submission-media.download', ['media' => $media->getKey()]))
            ->assertForbidden();
    });

    it('redirects a guest to login even with a valid signature', function () {
        $downloaded = false;

        Storage::disk('s3')->buildTemporaryUrlsUsing(function () use (&$downloaded): string {
            $downloaded = true;

            return 'https://s3.test/leaked';
        });

        [, $media] = createSubmissionFieldWithMedia('report.png');

        $url = URL::temporarySignedRoute(
            'submission-media.download',
            now()->addDay(),
            ['media' => $media->getKey()],
        );

        // The auth middleware must run before the file is served, so a valid signature alone
        // (with no authenticated panel user) cannot expose the download link.
        get($url)->assertRedirect(url('/'));

        expect($downloaded)->toBeFalse();
    });

    it('aborts when the media is not in the files collection', function () {
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn (): string => 'https://s3.test/x');

        asSuperAdmin();

        [, $media] = createSubmissionFieldWithMedia('report.png', 'not_files');

        $url = URL::temporarySignedRoute(
            'submission-media.download',
            now()->addDay(),
            ['media' => $media->getKey()],
        );

        get($url)->assertNotFound();
    });
});

describe('upload block submission state', function () {
    it('builds a sanitizer-safe signed download route instead of a raw storage url', function (string $block) {
        [$field, $media] = createSubmissionFieldWithMedia(nonLatin1FileName());

        $state = $block::getSubmissionState($field, null);

        $url = $state['media'][0]['temporary_url'];

        // The link is a clean, ASCII-only internal route, so it survives the submission HTML sanitizer.
        expect($url)->toContain('/submission-media/' . $media->getKey() . '/download')
            ->and(mb_check_encoding($url, 'ASCII'))->toBeTrue();

        $config = app(HtmlSanitizerConfig::class);
        $sanitized = (new HtmlSanitizer($config))->sanitize(
            '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '">download</a>',
        );

        expect($sanitized)->toMatch('/^<a href="[^"]+">download<\/a>$/');

        preg_match('/href="([^"]*)"/', $sanitized, $matches);

        // Decode first: the sanitizer re-encodes "=" as "&#61;", so a raw string
        // comparison fails even when the URL survives intact.
        expect(html_entity_decode($matches[1], ENT_QUOTES))->toBe($url);
    })->with([
        'UploadFormFieldBlock' => [UploadFormFieldBlock::class],
        'EducatableUploadFormFieldBlock' => [EducatableUploadFormFieldBlock::class],
    ]);

    it('strips the raw storage url that the signed route replaces', function () {
        [, $media] = createSubmissionFieldWithMedia(nonLatin1FileName());

        // Derive the URL from the real object key rather than building one by hand: the media
        // library keys objects as "{id}/{file name}", so the non-ASCII character is in the URL
        // path itself, percent-encoded the way a storage presigner encodes a key.
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn (string $path): string => 'https://s3.test/' . implode(
            '/',
            array_map(rawurlencode(...), explode('/', $path)),
        ));

        $rawUrl = $media->getTemporaryUrl(now()->addMinute());

        expect($rawUrl)->toContain(rawurlencode("\u{202F}"));

        $sanitized = (new HtmlSanitizer(app(HtmlSanitizerConfig::class)))->sanitize(
            '<a href="' . htmlspecialchars($rawUrl, ENT_QUOTES) . '">download</a>',
        );

        // This is the second defect the signed route fixes, and the reason an ASCII-safe
        // content disposition alone is not enough: the sanitizer deletes the link outright.
        expect($sanitized)->not->toContain('href');
    });
});
