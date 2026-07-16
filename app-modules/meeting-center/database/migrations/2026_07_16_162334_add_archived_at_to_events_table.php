<?php

use App\Features\EventArchivingFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('events', function (Blueprint $table) {
                $table->timestamp('archived_at')->nullable();
            });

            EventArchivingFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('archived_at');
            });

            EventArchivingFeature::deactivate();
        });
    }
};
