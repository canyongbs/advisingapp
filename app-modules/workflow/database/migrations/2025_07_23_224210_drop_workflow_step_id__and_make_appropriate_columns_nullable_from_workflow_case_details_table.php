<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_case_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workflow_step_id');
            $table->longText('close_details')->nullable()->change();
            $table->longText('res_details')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workflow_case_details', function (Blueprint $table) {
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');
            $table->longText('close_details')->change();
            $table->longText('res_details')->change();
        });
    }
};
