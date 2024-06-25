<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'caseload.view-any' => 'Caseload',
        'caseload.create' => 'Caseload',
        'caseload.*.view' => 'Caseload',
        'caseload.*.update' => 'Caseload',
        'caseload.*.delete' => 'Caseload',
        'caseload.*.restore' => 'Caseload',
        'caseload.*.force-delete' => 'Caseload',
        'caseload_subject.view-any' => 'Caseload Subject',
        'caseload_subject.create' => 'Caseload Subject',
        'caseload_subject.*.view' => 'Caseload Subject',
        'caseload_subject.*.update' => 'Caseload Subject',
        'caseload_subject.*.delete' => 'Caseload Subject',
        'caseload_subject.*.restore' => 'Caseload Subject',
        'caseload_subject.*.force-delete' => 'Caseload Subject',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('caseload_id');
        });

        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });

        DB::table('permission_groups')
            ->whereIn('name', ['Caseload', 'Caseload Subject'])
            ->delete();

        DB::table('caseload_subjects')->truncate();
        DB::table('caseloads')->truncate();
    }

    public function down(): void
    {
        DB::table('campaigns', function (Blueprint $table) {
            $table->foreignUuid('caseload_id')->nullable()->constrained('caseloads');
        });

        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });

        Schema::create('caseloads', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();
            $table->json('filters')->nullable();
            $table->string('model');
            $table->string('type');

            $table->foreignUuid('user_id')->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('caseload_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject_id');
            $table->string('subject_type');

            $table->foreignUuid('caseload_id')->constrained('caseloads')->cascadeOnDelete();

            $table->index(['subject_type', 'subject_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
