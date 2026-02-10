<?php

use AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogs\Pages\ManageLegacyAiMessageLogs;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageLegacyAiMessageLogs::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(ManageLegacyAiMessageLogs::getUrl())->assertForbidden();

    $user->givePermissionTo('assistant_chat_message_log.view-any');

    get(ManageLegacyAiMessageLogs::getUrl())->assertSuccessful();
});
