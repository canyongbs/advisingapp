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

use App\Models\Media;

describe('attachmentContentDisposition', function () {
    it('builds an ISO-8859-1 safe disposition for a file name that is not Latin-1 representable', function () {
        // macOS screenshots use a narrow no-break space (U+202F) before AM/PM, which S3 rejects in the header.
        $media = new Media();
        $media->file_name = "Screenshot-2026-09-08-at-7.48.39\u{202F}PM.jpg";

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ISO-8859-1'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain("filename*=utf-8''")
            ->and(rawurldecode($disposition))->toContain($media->file_name);
    });

    it('builds a simple disposition for an ASCII file name', function () {
        $media = new Media();
        $media->file_name = 'report.png';

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ISO-8859-1'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain('report.png')
            ->and($disposition)->not->toContain('filename*');
    });
});
