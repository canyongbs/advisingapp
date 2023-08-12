<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Engagement\Filament\Resources\EngagementFileResource;

// TODO: Add tests for the CreateEngagementFile
//test('A successful action on the CreateEngagementFile page', function () {});
//
//test('CreateEngagementFile requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateEngagementFile is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('create')
        )->assertForbidden();

    livewire(EngagementFileResource\Pages\CreateEngagementFile::class)
        ->assertForbidden();

    $user->givePermissionTo('engagement_file.view-any');
    $user->givePermissionTo('engagement_file.create');

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('create')
        )->assertSuccessful();

    // TODO: Test for file upload

    //$request = collect(CreateEngagementFileRequestFactory::new()->create());
    //
    //livewire(EngagementFileResource\Pages\CreateEngagementFile::class)
    //    ->fillForm($request->toArray())
    //    ->call('create')
    //    ->assertHasNoFormErrors();
    //
    //assertCount(1, EngagementFile::all());
    //
    //assertDatabaseHas(EngagementFile::class, $request->toArray());
});
