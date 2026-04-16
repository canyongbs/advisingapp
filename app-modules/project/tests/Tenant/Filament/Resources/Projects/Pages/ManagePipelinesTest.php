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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Pipeline\Settings\ProspectPipelineSettings;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageProjectPipelines;
use AdvisingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(ProspectPipelineSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();

    // Without any permissions
    get(ManageProjectPipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    // With only project permissions but not pipeline
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    get(ManageProjectPipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    // With both project and pipeline permissions
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');

    $user->refresh();

    get(ManageProjectPipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    // Now disable the setting — should be forbidden even with all permissions
    $settings->is_enabled = false;
    $settings->save();

    get(ManageProjectPipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can list pipelines when setting is enabled', function () {
    $settings = app(ProspectPipelineSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');

    actingAs($user);

    $project = Project::factory()->create();
    $group = Group::factory()->create();
    $pipelines = Pipeline::factory()->count(3)->for($project)->create([
        'user_id' => $user->id,
        'segment_id' => $group->id,
    ]);

    livewire(ManageProjectPipelines::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($pipelines);
});
