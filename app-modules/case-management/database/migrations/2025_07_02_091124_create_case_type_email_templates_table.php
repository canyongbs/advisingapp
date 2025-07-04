<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('case_type_email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_type_id')->constrained('case_types');
            $table->string('type');
            $table->unique(['case_type_id', 'type', 'role']);
            $table->jsonb('subject');
            $table->jsonb('body');
            $table->string('role')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_type_email_templates');
    }
};
