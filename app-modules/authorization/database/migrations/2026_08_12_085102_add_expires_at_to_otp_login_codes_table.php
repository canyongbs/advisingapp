<?php

use App\Features\OtpLoginFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('otp_login_codes', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable();
            });

            OtpLoginFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            OtpLoginFeature::deactivate();

            Schema::table('otp_login_codes', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        });
    }
};
