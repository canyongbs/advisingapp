<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_engagement_email_details', function (Blueprint $table) {
            $table->string('email_type')->initial('transactional');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_engagement_email_details', function (Blueprint $table) {
            $table->dropColumn('email_type');
        });
    }
};
