<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->boolean('is_embed_enabled')->default(false);
            $table->jsonb('authorized_domains')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->dropColumn('is_embed_enabled');
            $table->dropColumn('authorized_domains');
        });
    }
};
