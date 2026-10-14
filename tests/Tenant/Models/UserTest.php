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

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\Authenticatable;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Tests\setEnterpriseAiEnabled;

describe('enterprise ai', function () {
    it('does not report a `ConversationalAi` license while Enterprise AI is disabled, and restores it once re-enabled', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeFalse();

        assertDatabaseHas('licenses', [
            'user_id' => $user->getKey(),
            'type' => LicenseType::ConversationalAi,
            'deleted_at' => null,
        ]);

        setEnterpriseAiEnabled(true);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();
    });

    it('does not report a `ConversationalAi` license for a super admin while Enterprise AI is disabled', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeFalse();
    });

    it('still reports other licenses while Enterprise AI is disabled', function () {
        $user = User::factory()->licensed([LicenseType::ConversationalAi, LicenseType::RetentionCrm])->create();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::RetentionCrm))->toBeTrue()
            ->and($user->hasLicense([LicenseType::ConversationalAi, LicenseType::RetentionCrm]))->toBeFalse()
            ->and($user->hasAnyLicense([LicenseType::ConversationalAi, LicenseType::RetentionCrm]))->toBeTrue()
            ->and($user->hasAnyLicense(LicenseType::ConversationalAi))->toBeFalse();
    });

    it('does not grant a `ConversationalAi` license while Enterprise AI is disabled', function () {
        $user = User::factory()->create();

        setEnterpriseAiEnabled(false);

        expect($user->grantLicense(LicenseType::ConversationalAi))->toBeFalse();

        assertDatabaseMissing('licenses', [
            'user_id' => $user->getKey(),
            'type' => LicenseType::ConversationalAi,
        ]);
    });
});
