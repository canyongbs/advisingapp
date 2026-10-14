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

use AdvisingApp\Ai\Filament\Exports\AssistantUtilizationExporter;
use AdvisingApp\Report\Filament\Exports\UserExporter;
use App\Models\Export;
use App\Models\User;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\setEnterpriseAiEnabled;

beforeEach(function () {
    $csvDownloader = Mockery::mock(CsvDownloader::class);
    $csvDownloader->shouldReceive('__invoke')->andReturn(response()->streamDownload(fn () => print ('test,data'), 'test-export.csv'));

    app()->instance(CsvDownloader::class, $csvDownloader);
});

$createExport = function (User $user, string $exporter): Export {
    $export = new Export();
    $export->user()->associate($user);
    $export->file_name = 'test-export';
    $export->file_disk = 's3';
    $export->exporter = $exporter;
    $export->total_rows = 200;
    $export->completed_at = now();
    $export->save();

    return $export;
};

$downloadUrl = fn (Export $export): string => URL::signedRoute('filament.exports.download', ['authGuard' => 'web', 'export' => $export, 'format' => 'csv'], absolute: false);

it('grants each export ability only with its permission', function (string $ability, string $permission, bool $isRecordAbility) use ($createExport) {
    $user = User::factory()->create();
    $export = $createExport($user, UserExporter::class);
    $subject = $isRecordAbility ? $export : Export::class;

    expect($user->can($ability, $subject))->toBeFalse();

    $user->givePermissionTo($permission);

    expect($user->refresh()->can($ability, $subject))->toBeTrue();
})->with([
    'view' => ['view', 'export_hub.*.view', true],
    'update' => ['update', 'export_hub.*.update', true],
    'delete' => ['delete', 'export_hub.*.delete', true],
    'delete any' => ['deleteAny', 'export_hub.*.delete', false],
    'restore' => ['restore', 'export_hub.*.restore', true],
    'restore any' => ['restoreAny', 'export_hub.*.restore', false],
]);

it('does not allow downloading an export from its notification without the `export_hub.*.view` permission', function () use ($createExport, $downloadUrl) {
    $user = User::factory()->create();

    actingAs($user);

    get($downloadUrl($createExport($user, UserExporter::class)))->assertForbidden();
});

describe('enterprise ai', function () use ($createExport, $downloadUrl) {
    it('forbids downloading an Enterprise AI export from its notification while Enterprise AI is disabled', function () use ($createExport, $downloadUrl) {
        $user = User::factory()->create();
        $user->givePermissionTo('export_hub.*.view');

        actingAs($user);

        $export = $createExport($user, AssistantUtilizationExporter::class);

        get($downloadUrl($export))->assertSuccessful();

        setEnterpriseAiEnabled(false);

        get($downloadUrl($export))->assertForbidden();
    });

    it('allows downloading other exports from their notification while Enterprise AI is disabled', function () use ($createExport, $downloadUrl) {
        $user = User::factory()->create();
        $user->givePermissionTo('export_hub.*.view');

        actingAs($user);

        $export = $createExport($user, UserExporter::class);

        setEnterpriseAiEnabled(false);

        get($downloadUrl($export))->assertSuccessful();
    });
});
