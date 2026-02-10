<?php

use AdvisingApp\Report\Filament\Pages\UserLoginActivity;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(UserLoginActivity::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(UserLoginActivity::getUrl())->assertSuccessful();
});