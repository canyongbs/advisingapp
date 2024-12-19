<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard): void {
                $roleHasAssistantViewAnyPermissions = DB::table('role_has_permissions')
                    ->where('permission_id', DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->where('name', 'assistant.view-any')
                        ->value('id'))
                    ->get();

                $assistantViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'assistant.*.view')
                    ->value('id');

                DB::table('role_has_permissions')
                    ->insert(
                        $roleHasAssistantViewAnyPermissions
                            ->map(fn (object $roleHasPermission): array => [
                                'permission_id' => $assistantViewPermissionId,
                                'role_id' => $roleHasPermission->id,
                            ])
                            ->all(),
                    );

                $roleHasRealtimeChatViewAnyPermissions = DB::table('role_has_permissions')
                    ->where('permission_id', DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->where('name', 'realtime_chat.view-any')
                        ->value('id'))
                    ->get();

                $realtimeChatViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'realtime_chat.*.view')
                    ->value('id');

                DB::table('role_has_permissions')
                    ->insert(
                        $roleHasRealtimeChatViewAnyPermissions
                            ->map(fn (object $roleHasPermission): array => [
                                'permission_id' => $realtimeChatViewPermissionId,
                                'role_id' => $roleHasPermission->id,
                            ])
                            ->all(),
                    );
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard): void {
                $assistantViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'assistant.*.view')
                    ->value('id');

                DB::table('role_has_permissions')
                    ->where('permission_id', $assistantViewPermissionId)
                    ->delete();

                $realtimeChatViewPermissionId = DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->where('name', 'realtime_chat.*.view')
                    ->value('id');

                DB::table('role_has_permissions')
                    ->where('permission_id', $realtimeChatViewPermissionId)
                    ->delete();
            });
    }
};
