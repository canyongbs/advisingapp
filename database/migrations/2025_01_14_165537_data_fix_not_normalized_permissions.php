<?php

use AdvisingApp\Authorization\Models\Permission;
use AdvisingApp\Authorization\Models\PermissionGroup;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            collect($this->guards)
                ->each(function (string $guard) {
                    $this->deletePermissions([
                        'consent_agreement.update',
                    ], $guard);
                });

            Permission::query()
                ->where('name', 'like', 'realtime_chat.%')
                ->update([
                    'group_id' => PermissionGroup::query()
                        ->where('name', 'Realtime Chat')
                        ->value('id'),
                ]);

            $this->deletePermissions([
                'realtime_chat.*.view',
            ], 'api');
        });
    }

    public function down(): void
    {
        // These permissions and groups are not normalized and do not need to be recreated if this migration fails.
    }
};
