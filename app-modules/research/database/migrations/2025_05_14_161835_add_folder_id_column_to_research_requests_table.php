<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('research_requests', function (Blueprint $table) {
            $table->foreignUuid('folder_id')->nullable()->constrained('research_request_folders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('research_requests', function (Blueprint $table) {
            $table->dropForeignId(['folder_id']);
        });
    }
};
