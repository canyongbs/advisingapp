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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Models\EngagementFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$createEngagementFileWithMedia = function (): EngagementFile {
    $file = EngagementFile::factory()->create();

    $file
        ->addMedia(UploadedFile::fake()->image('upload.png'))
        ->usingName('Quarterly Report')
        ->usingFileName(Str::random(40) . '.png')
        ->toMediaCollection('file');

    return $file;
};

beforeEach(function () {
    Storage::fake('s3');
});

it('redirects to a temporary url that downloads the file under its original name', function () use ($createEngagementFileWithMedia) {
    $captured = null;

    Storage::disk('s3')->buildTemporaryUrlsUsing(function (string $path, DateTimeInterface $expiration, array $options) use (&$captured): string {
        $captured = $options['ResponseContentDisposition'] ?? null;

        return 'https://s3.test/' . $path;
    });

    asSuperAdmin();

    $file = $createEngagementFileWithMedia();

    get(route('engagement-file-download', ['file' => $file]))->assertRedirect();

    expect($captured)->toBe('attachment; filename="Quarterly Report.png"');
});

describe('authorization', function () use ($createEngagementFileWithMedia) {
    it('denies the download without the engagement file view permission', function () use ($createEngagementFileWithMedia) {
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn (string $path): string => 'https://s3.test/' . $path);

        $file = $createEngagementFileWithMedia();

        actingAs(User::factory()->licensed(LicenseType::cases())->create());

        get(route('engagement-file-download', ['file' => $file]))->assertForbidden();
    });

    it('allows the download with the engagement file view permission', function () use ($createEngagementFileWithMedia) {
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn (string $path): string => 'https://s3.test/' . $path);

        $file = $createEngagementFileWithMedia();

        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('engagement_file.view-any');
        $user->givePermissionTo('engagement_file.*.view');

        actingAs($user);

        get(route('engagement-file-download', ['file' => $file]))->assertRedirect();
    });
});
