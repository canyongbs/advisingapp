<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $alertToConcernPermissions
     */
    private array $alertToConcernPermissions = [
        'alert.*.delete' => 'concern.*.delete',
        'alert.*.force-delete' => 'concern.*.force-delete',
        'alert.*.restore' => 'concern.*.restore',
        'alert.*.update' => 'concern.*.update',
        'alert.*.view' => 'concern.*.view',
        'alert.create' => 'concern.create',
        'alert.view-any' => 'concern.view-any',
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
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->alertToConcernPermissions, $guard);
            });

            $this->renamePermissionGroups(['Alert' => 'Concern']);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->alertToConcernPermissions), $guard);
            });

            $this->renamePermissionGroups(['Concern' => 'Alert']);
        });
    }
};
