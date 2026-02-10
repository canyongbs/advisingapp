<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ArtificialIntelligence;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ArtificialIntelligence::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(ArtificialIntelligence::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ArtificialIntelligence::getUrl())->assertSuccessful();
});