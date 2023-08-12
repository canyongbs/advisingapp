<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

// TODO: Add tests for the ViewEngagementFile
test('The correct details are displayed on the ViewEngagementFile page', function () {});

// Permission Tests

test('ViewEngagementFile is gated with proper access control', function () {
    $user = User::factory()->create();

    $engagementFile = EngagementFile::factory()->create();

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('view', [
                'record' => $engagementFile,
            ])
        )->assertForbidden();

    $user->givePermissionTo('engagement_file.view-any');
    $user->givePermissionTo('engagement_file.*.view');

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('view', [
                'record' => $engagementFile,
            ])
        )->assertSuccessful();
});
