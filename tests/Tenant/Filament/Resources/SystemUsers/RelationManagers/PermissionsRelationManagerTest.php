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

use AdvisingApp\Authorization\Models\Permission;
use App\Filament\Resources\SystemUsers\Pages\EditSystemUser;
use App\Filament\Resources\SystemUsers\RelationManagers\PermissionsRelationManager;
use App\Models\SystemUser;
use Filament\Actions\AttachAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

describe('enterprise ai', function () {
    it('hides Enterprise AI permissions while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $systemUser = SystemUser::factory()->create();
        $systemUser->givePermissionTo(['prompt.view-any', 'user.view-any']);

        $aiPermission = Permission::query()->where('name', 'prompt.view-any')->where('guard_name', 'api')->firstOrFail();
        $otherPermission = Permission::query()->where('name', 'user.view-any')->where('guard_name', 'api')->firstOrFail();

        livewire(PermissionsRelationManager::class, ['ownerRecord' => $systemUser, 'pageClass' => EditSystemUser::class])
            ->assertCanSeeTableRecords([$aiPermission, $otherPermission]);

        setEnterpriseAiEnabled(false);

        livewire(PermissionsRelationManager::class, ['ownerRecord' => $systemUser, 'pageClass' => EditSystemUser::class])
            ->assertCanSeeTableRecords([$otherPermission])
            ->assertCanNotSeeTableRecords([$aiPermission]);
    });

    it('does not offer Enterprise AI permissions to attach while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $systemUser = SystemUser::factory()->create();

        $aiPermission = Permission::query()->where('name', 'prompt.view-any')->where('guard_name', 'api')->firstOrFail();
        $otherPermission = Permission::query()->where('name', 'user.view-any')->where('guard_name', 'api')->firstOrFail();

        $searchAttachOptions = function (string $search) use ($systemUser): array {
            $results = [];

            livewire(PermissionsRelationManager::class, ['ownerRecord' => $systemUser, 'pageClass' => EditSystemUser::class])
                ->mountAction(TestAction::make(AttachAction::class)->table())
                ->assertFormFieldExists('recordId', 'mountedActionSchema0', function (Select $field) use ($search, &$results): bool {
                    $results = $field->getSearchResults($search);

                    return true;
                });

            return $results;
        };

        expect($searchAttachOptions('prompt.view-any'))->toHaveKey($aiPermission->getKey())
            ->and($searchAttachOptions('user.view-any'))->toHaveKey($otherPermission->getKey());

        setEnterpriseAiEnabled(false);

        expect($searchAttachOptions('prompt.view-any'))->not->toHaveKey($aiPermission->getKey())
            ->and($searchAttachOptions('user.view-any'))->toHaveKey($otherPermission->getKey());
    });
});
