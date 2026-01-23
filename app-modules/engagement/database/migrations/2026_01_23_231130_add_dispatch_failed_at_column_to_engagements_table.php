<?php

use App\Features\EngagementDispatchFailedAtFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('engagements', function (Blueprint $table) {
                $table->timestamp('dispatch_failed_at')->nullable();
            });

            EngagementDispatchFailedAtFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            EngagementDispatchFailedAtFeature::deactivate();

            Schema::table('engagements', function (Blueprint $table) {
                $table->dropColumn('dispatch_failed_at');
            });
        });
    }
};
