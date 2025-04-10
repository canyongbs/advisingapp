<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string> $permissionsToCreate
     */
    private array $permissionsToCreate = [
        'assistant.*.view' => 'Assistant',
        'realtime_chat.*.view' => 'Realtime Chat',
    ];

    /**
     * @var array<string> $permissionsToDelete
     */
    private array $permissionsToDelete = [
        'campaign.view_campaign_settings' => 'Campaign',
        'engagement.view_message_center' => 'Engagement',
        'report.access_assistant' => 'Report',
        'report.access_assistant_settings' => 'Report',
        'timeline.access' => 'Timeline',
        'assistant_chat.*.delete' => 'Assistant Chat',
        'assistant_chat.*.force-delete' => 'Assistant Chat',
        'assistant_chat.*.restore' => 'Assistant Chat',
        'assistant_chat.*.update' => 'Assistant Chat',
        'assistant_chat.*.view' => 'Assistant Chat',
        'assistant_chat.create' => 'Assistant Chat',
        'assistant_chat.view-any' => 'Assistant Chat',
        'assistant_chat_folder.*.delete' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.force-delete' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.restore' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.update' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.view' => 'Assistant Chat Folder',
        'assistant_chat_folder.create' => 'Assistant Chat Folder',
        'assistant_chat_folder.view-any' => 'Assistant Chat Folder',
        'assistant_chat_message.*.delete' => 'Assistant Chat Message',
        'assistant_chat_message.*.force-delete' => 'Assistant Chat Message',
        'assistant_chat_message.*.restore' => 'Assistant Chat Message',
        'assistant_chat_message.*.update' => 'Assistant Chat Message',
        'assistant_chat_message.*.view' => 'Assistant Chat Message',
        'assistant_chat_message.create' => 'Assistant Chat Message',
        'assistant_chat_message.view-any' => 'Assistant Chat Message',
        'authorization.impersonate' => 'Authorization',
        'authorization.view_api_documentation' => 'Authorization',
        'authorization.view_dashboard' => 'Authorization',
        'authorization.view_product_health_dashboard' => 'Authorization',
        'student.tags.manage' => 'Student',
        'prospect.tags.manage' => 'Prospect',
    ];

    /**
     * @var array<string> $permissionsToRename
     */
    private array $permissionsToRename = [
        'ai_assistant.*.delete' => 'assistant_custom.*.delete',
        'ai_assistant.*.force-delete' => 'assistant_custom.*.force-delete',
        'ai_assistant.*.restore' => 'assistant_custom.*.restore',
        'ai_assistant.*.update' => 'assistant_custom.*.update',
        'ai_assistant.*.view' => 'assistant_custom.*.view',
        'ai_assistant.create' => 'assistant_custom.create',
        'ai_assistant.view-any' => 'assistant_custom.view-any',
        'assistant.access' => 'assistant.view-any',
        'in-app-communication.realtime-chat.access' => 'realtime_chat.view-any',
    ];

    /**
     * @var array<string> $permissionGroupsToRename
     */
    private array $permissionGroupsToRename = [
        'Ai Assistant' => 'Assistant Custom',
        'In-App Communication' => 'Realtime Chat',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissionsToCreate, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);

                $this->deletePermissions(array_keys($this->permissionsToDelete), $guard);

                $this->renamePermissions($this->permissionsToRename, $guard);

                $this->renamePermissionGroups($this->permissionGroupsToRename);

                $modelHasAssistantViewAnyPermissions = DB::table('model_has_permissions')
                    ->where('permission_id', DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->where('name', 'assistant.view-any')
                        ->value('id'))
                    ->get();

                $assistantViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'assistant.*.view')
                    ->value('id');

                DB::table('model_has_permissions')
                    ->insert(
                        $modelHasAssistantViewAnyPermissions
                            ->map(fn (object $modelHasPermission): array => [
                                'permission_id' => $assistantViewPermissionId,
                                'model_type' => $modelHasPermission->model_type,
                                'model_id' => $modelHasPermission->model_id,
                            ])
                            ->all(),
                    );

                $modelHasRealtimeChatViewAnyPermissions = DB::table('model_has_permissions')
                    ->where('permission_id', DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->where('name', 'realtime_chat.view-any')
                        ->value('id'))
                    ->get();

                $realtimeChatViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'realtime_chat.*.view')
                    ->value('id');

                DB::table('model_has_permissions')
                    ->insert(
                        $modelHasRealtimeChatViewAnyPermissions
                            ->map(fn (object $modelHasPermission): array => [
                                'permission_id' => $realtimeChatViewPermissionId,
                                'model_type' => $modelHasPermission->model_type,
                                'model_id' => $modelHasPermission->model_id,
                            ])
                            ->all(),
                    );
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->renamePermissionGroups(array_flip($this->permissionGroupsToRename));

                $this->renamePermissions(array_flip($this->permissionsToRename), $guard);

                $permissions = Arr::except($this->permissionsToDelete, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);

                $this->deletePermissions(array_keys($this->permissionsToCreate), $guard);

                DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'assistant.view-any')
                    ->update([
                        'name' => 'assistant.access',
                    ]);
            });
    }
};
