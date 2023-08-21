<?php

use App\Models\User;
use Assist\Audit\Models\Audit;

use function Pest\Laravel\actingAs;

use Assist\Audit\Filament\Resources\AuditResource;

// TODO: Write tests for ViewAudit page
test('The correct details are displayed on the ViewAudit page', function () {});

// Permission Tests

test('ViewAudit is gated with proper access control', function () {
    $user = User::factory()->create();

    $audit = Audit::factory()->create();

    actingAs($user)
        ->get(
            AuditResource::getUrl('view', [
                'record' => $audit,
            ])
        )->assertForbidden();

    $user->givePermissionTo('audit.view-any');
    $user->givePermissionTo('audit.*.view');

    actingAs($user)
        ->get(
            AuditResource::getUrl('view', [
                'record' => $audit,
            ])
        )->assertSuccessful();
});
