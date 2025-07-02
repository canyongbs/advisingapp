<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->string('assignment_type')->default('none');
            $table->foreignUuid('assignment_type_individual_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
            $table->dropConstrainedForeignId(['assignment_type_individual_id']);
        });
    }
};
