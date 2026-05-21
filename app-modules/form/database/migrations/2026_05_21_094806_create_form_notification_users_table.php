<?php

use App\Features\FormsNotificationFeature;
use Google\Service\Forms;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_notification_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['form_id', 'user_id']);
            $table->timestamps();
        });

        FormsNotificationFeature::activate();
    }

    public function down(): void
    {
        FormsNotificationFeature::deactivate();
        Schema::dropIfExists('form_notification_users');
    }
};
