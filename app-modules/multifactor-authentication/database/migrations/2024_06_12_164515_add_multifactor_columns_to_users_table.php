<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('multifactor_secret')
                ->nullable();

            $table->text('multifactor_recovery_codes')
                ->nullable();

            $table->timestamp('multifactor_confirmed_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'multifactor_secret',
                'multifactor_recovery_codes',
                'multifactor_confirmed_at',
            ]);
        });
    }
};
