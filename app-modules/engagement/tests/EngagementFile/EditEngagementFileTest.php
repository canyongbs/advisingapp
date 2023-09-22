<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

// TODO: Add tests for the EditEngagementFile
//test('A successful action on the EditEngagementFile page', function () {});
//
//test('EngagementFile requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditEngagementFile is gated with proper access control', function () {
    $user = User::factory()->create();

    $engagementFile = EngagementFile::factory()->create();

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('edit', [
                'record' => $engagementFile,
            ])
        )->assertForbidden();

    livewire(EngagementFileResource\Pages\EditEngagementFile::class, [
        'record' => $engagementFile->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('engagement_file.view-any');
    $user->givePermissionTo('engagement_file.*.update');

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('edit', [
                'record' => $engagementFile,
            ])
        )->assertSuccessful();

    // TODO: Test for file upload

    //$request = collect(EditEngagementFileRequestFactory::new()->create());
    //
    //livewire(EngagementFileResource\Pages\EditEngagementFile::class, [
    //    'record' => $engagementFile->getRouteKey(),
    //])
    //    ->fillForm($request->toArray())
    //    ->call('save')
    //    ->assertHasNoFormErrors();
    //
    //assertEquals($request['description'], $engagementFile->fresh()->description);
});
