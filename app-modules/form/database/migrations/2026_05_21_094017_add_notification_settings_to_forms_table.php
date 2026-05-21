<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('notify_to_care_team')->default(false);
            $table->boolean('notify_to_subscibers')->default(false);
            $table->boolean('notify_via_app')->default(false);
            $table->boolean('notify_via_email')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['notify_via_app', 'notify_via_email']);
        });
    }
};
