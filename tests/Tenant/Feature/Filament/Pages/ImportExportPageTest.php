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

use AdvisingApp\Report\Filament\Exports\UserExporter;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Filament\Imports\UserImporter;
use App\Filament\Pages\ImportExport;
use App\Livewire\ExportsTable;
use App\Livewire\ImportsTable;
use App\Models\Export;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

// Access Control Tests

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ImportExport::getUrl())->assertForbidden();

    $user->givePermissionTo('export_hub.view-any');

    get(ImportExport::getUrl())->assertSuccessful();
});

it('forbids the page when student editing is disabled and the user only has record sync access', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('record_sync.view-any');

    actingAs($user);

    get(ImportExport::getUrl())->assertForbidden();
});

it('allows the page when student editing is enabled and the user only has record sync access', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('record_sync.view-any');

    actingAs($user);

    get(ImportExport::getUrl())->assertSuccessful();
});

it('renders the import/export page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    get(ImportExport::getUrl())
        ->assertSuccessful()
        ->assertSeeText(['Import', 'Export']);
});

// Import Tab Tests

it('renders the import table', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    livewire(ImportsTable::class)
        ->assertSuccessful();
});

it('displays import records in the import table', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = UserImporter::class;
    $import->total_rows = 100;
    $import->save();

    livewire(ImportsTable::class)
        ->assertCanSeeTableRecords([$import]);
});

it('shows download button when import is completed and file exists and user has permission', function () {
    Storage::fake('s3');

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('export_hub.import');

    actingAs($user);

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = UserImporter::class;
    $import->total_rows = 50;
    $import->completed_at = now();
    $import->save();

    Storage::disk('s3')->put("imports/{$import->getKey()}.csv", 'test,data');

    livewire(ImportsTable::class)
        ->assertTableActionVisible('download', $import);
});

it('hides download button when import is not completed', function () {
    Storage::fake('s3');

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('export_hub.import');

    actingAs($user);

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = UserImporter::class;
    $import->total_rows = 50;
    $import->save();

    Storage::disk('s3')->put("imports/{$import->getKey()}.csv", 'test,data');

    livewire(ImportsTable::class)
        ->assertTableActionHidden('download', $import);
});

it('hides download button when import file does not exist on disk', function () {
    Storage::fake('s3');

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('export_hub.import');

    actingAs($user);

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = UserImporter::class;
    $import->total_rows = 50;
    $import->completed_at = now();
    $import->save();

    livewire(ImportsTable::class)
        ->assertTableActionHidden('download', $import);
});

it('hides download button when user lacks export_hub.import permission', function () {
    Storage::fake('s3');

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'test-import.csv';
    $import->file_path = '/tmp/test-import.csv';
    $import->importer = UserImporter::class;
    $import->total_rows = 50;
    $import->completed_at = now();
    $import->save();

    Storage::disk('s3')->put("imports/{$import->getKey()}.csv", 'test,data');

    livewire(ImportsTable::class)
        ->assertTableActionHidden('download', $import);
});

// Export Tab Tests

it('displays export records in the export table', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    $export = new Export();
    $export->user()->associate($user);
    $export->file_name = 'test-export.csv';
    $export->file_disk = 's3';
    $export->exporter = UserExporter::class;
    $export->total_rows = 200;
    $export->save();

    livewire(ExportsTable::class)
        ->assertCanSeeTableRecords([$export]);
});

it('shows the export download button when the export is completed and the user has permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('export_hub.import');

    actingAs($user);

    $export = new Export();
    $export->user()->associate($user);
    $export->file_name = 'test-export.csv';
    $export->file_disk = 's3';
    $export->exporter = UserExporter::class;
    $export->total_rows = 200;
    $export->completed_at = now();
    $export->save();

    livewire(ExportsTable::class)
        ->assertTableActionVisible('download', $export);
});

it('hides the export download button when the export is not completed', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('export_hub.import');

    actingAs($user);

    $export = new Export();
    $export->user()->associate($user);
    $export->file_name = 'test-export.csv';
    $export->file_disk = 's3';
    $export->exporter = UserExporter::class;
    $export->total_rows = 200;
    $export->save();

    livewire(ExportsTable::class)
        ->assertTableActionHidden('download', $export);
});

it('hides the export download button when the user lacks the export_hub.import permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    $export = new Export();
    $export->user()->associate($user);
    $export->file_name = 'test-export.csv';
    $export->file_disk = 's3';
    $export->exporter = UserExporter::class;
    $export->total_rows = 200;
    $export->completed_at = now();
    $export->save();

    livewire(ExportsTable::class)
        ->assertTableActionHidden('download', $export);
});

// Student Sync Tab Tests

it('does not show student sync tab when student editing is disabled', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('record_sync.view-any');

    actingAs($user);

    get(ImportExport::getUrl())
        ->assertSuccessful()
        ->assertDontSeeText('Student Sync');
});

it('does not show student sync tab when user lacks record_sync.view-any permission', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    get(ImportExport::getUrl())
        ->assertSuccessful()
        ->assertDontSeeText('Student Sync');
});

it('shows student sync tab when student editing is enabled and user has permission', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');
    $user->givePermissionTo('record_sync.view-any');

    actingAs($user);

    get(ImportExport::getUrl())
        ->assertSuccessful()
        ->assertSeeText('Student Sync');
});

// Active Tab Tests

it('defaults the active tab to import when the user has export hub access', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    livewire(ImportExport::class)
        ->assertSet('activeTab', 'import');
});

it('defaults the active tab to student sync when the user can only access student sync', function () {
    $settings = app(ManageStudentConfigurationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('record_sync.view-any');

    actingAs($user);

    livewire(ImportExport::class)
        ->assertSet('activeTab', 'student-sync');
});

it('syncs the active tab into the component state when switching tabs', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    livewire(ImportExport::class)
        ->assertSet('activeTab', 'import')
        ->set('activeTab', 'export')
        ->assertSet('activeTab', 'export');
});

it('marks the active tab as current', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('export_hub.view-any');

    actingAs($user);

    $html = livewire(ImportExport::class)
        ->set('activeTab', 'export')
        ->html();

    $activeLabel = null;

    if (preg_match('/<button\b(?=[^>]*aria-selected="true")[^>]*>(.*?)<\/button>/is', $html, $match)) {
        $activeLabel = trim(preg_replace('/\s+/', ' ', strip_tags($match[1])));
    }

    expect($activeLabel)->toContain('Export');
});
