<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->integer('portal_view_count')->after('category_id')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->dropColumn('portal_view_count');
        });
    }
};
