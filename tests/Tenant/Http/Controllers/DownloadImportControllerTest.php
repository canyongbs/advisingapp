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

use AdvisingApp\Ai\Filament\Imports\EmployeeAdvisorQuestionImporter;
use App\Filament\Imports\UserImporter;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\setEnterpriseAiEnabled;

beforeEach(function () {
    Storage::fake('s3');
});

$createImport = function (User $user, string $importer): Import {
    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = $importer;
    $import->total_rows = 50;
    $import->completed_at = now();
    $import->save();

    Storage::disk('s3')->put("imports/{$import->getKey()}.csv", 'test,data');

    return $import;
};

describe('enterprise ai', function () use ($createImport) {
    it('forbids downloading an Enterprise AI import while Enterprise AI is disabled', function () use ($createImport) {
        $user = User::factory()->create();
        $user->givePermissionTo('export_hub.import');

        actingAs($user);

        $import = $createImport($user, EmployeeAdvisorQuestionImporter::class);
        $downloadUrl = URL::signedRoute('imports.download', ['import' => $import]);

        get($downloadUrl)->assertSuccessful();

        setEnterpriseAiEnabled(false);

        get($downloadUrl)->assertForbidden();
    });

    it('allows downloading other imports while Enterprise AI is disabled', function () use ($createImport) {
        $user = User::factory()->create();
        $user->givePermissionTo('export_hub.import');

        actingAs($user);

        $import = $createImport($user, UserImporter::class);

        setEnterpriseAiEnabled(false);

        get(URL::signedRoute('imports.download', ['import' => $import]))->assertSuccessful();
    });
});
