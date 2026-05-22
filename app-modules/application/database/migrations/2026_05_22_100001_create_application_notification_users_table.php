<?php

use App\Features\FormsNotificationFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('application_notification_users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('application_id')->constrained('applications')->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->unique(['application_id', 'user_id']);
                $table->timestamps();
            });

            FormsNotificationFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            FormsNotificationFeature::deactivate();

            Schema::dropIfExists('application_notification_users');
        });
    }
};
