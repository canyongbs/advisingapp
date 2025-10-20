<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $segmentToGroupPermissions
     */
    private array $segmentToGroupPermissions = [
        'segment.*.delete' => 'group.*.delete',
        'segment.*.force-delete' => 'group.*.force-delete',
        'segment.*.restore' => 'group.*.restore',
        'segment.*.update' => 'group.*.update',
        'segment.*.view' => 'group.*.view',
        'segment.create' => 'group.create',
        'segment.view-any' => 'group.view-any',
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
                $this->renamePermissions($this->segmentToGroupPermissions, $guard);
            });

            $this->renamePermissionGroups([
                'Segment' => 'Group',
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->segmentToGroupPermissions), $guard);
            });

            $this->renamePermissionGroups([
                'Group' => 'Segment',
            ]);
        });
    }
};
