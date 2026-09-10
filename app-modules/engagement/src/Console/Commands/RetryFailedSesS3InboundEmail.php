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

namespace AdvisingApp\Engagement\Console\Commands;

use AdvisingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RetryFailedSesS3InboundEmail extends Command
{
    protected $signature = 'engagement:retry-failed-inbound-email {path : Path to the failed inbound email on the s3-inbound-email disk (e.g. failed/<id>)}';

    protected $description = 'Move a failed inbound email back out of the failed folder and queue it for reprocessing';

    public function handle(): int
    {
        $path = $this->normalizePath((string) $this->argument('path'));

        $disk = Storage::disk('s3-inbound-email');

        if (! $disk->exists($path)) {
            $this->error("File not found on the s3-inbound-email disk: {$path}");

            return static::FAILURE;
        }

        // Move the file back to the disk root (its original pending location) so that a re-failure
        // lands at `failed/<id>` rather than nesting under `failed/failed/<id>`, and so the queued
        // job shares the same unique id the scheduled gatherer would use — letting the job's
        // ShouldBeUnique lock dedupe the two and prevent double-processing.
        $originalPath = Str::startsWith($path, 'failed/') ? Str::after($path, 'failed/') : $path;

        if ($originalPath !== $path) {
            $disk->move($path, $originalPath);
        }

        // Queued (not run synchronously) so the ShouldBeUnique lock applies. The job may still
        // instantly re-fail and land back in `failed/`, so this only reports that it was queued.
        dispatch(new ProcessSesS3InboundEmail($originalPath));

        $this->info("Queued inbound email for reprocessing: {$originalPath}");

        return static::SUCCESS;
    }

    protected function normalizePath(string $path): string
    {
        $path = trim($path);

        // Reduce an s3://<bucket>/<key> URI down to just the object key.
        if (Str::startsWith($path, 's3://')) {
            $path = Str::after(Str::after($path, 's3://'), '/');
        }

        $path = ltrim($path, '/');

        // Strip the disk root prefix when a full object key was provided.
        $root = trim(Config::string('filesystems.disks.s3-inbound-email.root'), '/');

        if ($root !== '' && Str::startsWith($path, "{$root}/")) {
            $path = Str::after($path, "{$root}/");
        }

        return $path;
    }
}
