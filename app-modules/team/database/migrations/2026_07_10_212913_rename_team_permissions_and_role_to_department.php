<?php

use App\Features\RenameTeamToDepartmentFeature;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'team.*.delete' => 'department.*.delete',
        'team.*.force-delete' => 'department.*.force-delete',
        'team.*.restore' => 'department.*.restore',
        'team.*.update' => 'department.*.update',
        'team.*.view' => 'department.*.view',
        'team.create' => 'department.create',
        'team.view-any' => 'department.view-any',
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
                $this->renamePermissions($this->permissions, $guard);
            });

            $this->renamePermissionGroups([
                'Team' => 'Department',
            ]);
            
            RenameTeamToDepartmentFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            RenameTeamToDepartmentFeature::deactivate();

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->permissions), $guard);
            });

            $this->renamePermissionGroups([
                'Department' => 'Team',
            ]);
        });
    }
};
