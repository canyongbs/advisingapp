<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageFiles;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Models\ProjectFile;
use App\Models\User;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageFiles::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $project->createdBy()->associate($user);
    $project->save();
    $user->refresh();

    get(ManageFiles::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can list files', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    ProjectFile::factory()->count(5)->for($project)->create();

    livewire(ManageFiles::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->files);
});

it('can validate create files inputs', function (array $data, mixed $errors) {
    asSuperAdmin();
    Storage::fake('s3');

    $project = Project::factory()->create();
    $file = ProjectFile::factory()->make($data);

    livewire(ManageFiles::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('create', data: $file->toArray())
        ->assertHasTableActionErrors();

    assertDatabaseMissing(
        ProjectFile::class,
        $file->toArray()
    );
})->with([
    '`description` is required' => [['description' => null], 'description', 'The description field is required.'],
    '`description` is max 255 characters' => [['description' => str_repeat('a', 256)], 'description', 'The description may not be greater than 255 characters.'],
]);

it('can create files', function () {
    asSuperAdmin();
    Storage::fake('s3');

    $project = Project::factory()->create();
    $fakeFile = UploadedFile::fake()->image(fake()->word . '.png');

    livewire(ManageFiles::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('create', data: [
            'description' => 'Test File',
            'file' => $fakeFile,
        ])
        ->assertHasNoTableActionErrors();

    assertCount(1, ProjectFile::all());
});

it('can edit files', function () {
    asSuperAdmin();
    Storage::fake('s3');

    $project = Project::factory()->create();
    $fakeFile = UploadedFile::fake()->image(fake()->word . '.png');
    $file = ProjectFile::factory()->state([
        'project_id' => $project->id,
        'description' => 'Test File',
    ])->create();
    $file->addMedia($fakeFile)->toMediaCollection('file');

    $request = ProjectFile::factory()->make([
        'project_id' => $project->id,
        'description' => 'Changed Test File',
        'retention_date' => now()->addDays(7)->toDateString(),
    ]);

    livewire(ManageFiles::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('edit', record: $file->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        ProjectFile::class,
        $request->toArray()
    );
});

it('correctly prunes ProjectFiles based on retention_date', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $expiredFile = ProjectFile::factory()->create([
        'project_id' => $project->id,
        'retention_date' => fake()->dateTimeBetween('-1 year', '-1 day'),
    ]);

    $noRetentionDateFile = ProjectFile::factory()->create([
        'project_id' => $project->id,
        'retention_date' => null,
    ]);

    $futureRetentionDateFile = ProjectFile::factory()->create([
        'project_id' => $project->id,
        'retention_date' => fake()->dateTimeBetween('+1 day', '+ 1 year'),
    ]);

    artisan(PruneCommand::class, [
        '--model' => ProjectFile::class,
    ])->assertExitCode(0);

    assertModelMissing($expiredFile);
    assertModelExists($noRetentionDateFile);
    assertModelExists($futureRetentionDateFile);
});

it('is scheduled to prune ProjectFiles daily during scheduler run', function () {
    $schedule = app()->make(Schedule::class);

    $events = (new Collection($schedule->events()))->filter(function (Event $event) {
        $fileClass = preg_quote(ProjectFile::class);

        return preg_match("/model:prune\s--model=.*{$fileClass}.*/", $event->command)
            && $event->expression === '0 0 * * *';
    });

    expect($events)->toHaveCount(1);
});
