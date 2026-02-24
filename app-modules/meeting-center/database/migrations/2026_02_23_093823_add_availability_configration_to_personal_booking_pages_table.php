<?php

use App\Features\PersonalBookingAvailabilityFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('personal_booking_pages', function (Blueprint $table) {
                $table->boolean('is_default_appointment_buffer_enabled')->default(false);
                $table->unsignedInteger('default_appointment_buffer_before_duration')->default(0);
                $table->unsignedInteger('default_appointment_buffer_after_duration')->default(0);
                $table->jsonb('available_appointment_hours')->nullable();
            });

            PersonalBookingAvailabilityFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            PersonalBookingAvailabilityFeature::deactivate();
            Schema::table('personal_booking_pages', function (Blueprint $table) {
                $table->dropColumn([
                    'is_default_appointment_buffer_enabled',
                    'default_appointment_buffer_before_duration',
                    'default_appointment_buffer_after_duration',
                    'available_appointment_hours',
                ]);
            });
        });
    }
};
