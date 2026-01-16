<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $exists = DB::table('alert_configurations')
                ->where('preset', 'concern_raised')
                ->exists();

            if ($exists) {
                return;
            }

            DB::table('alert_configurations')->insert([
                'id' => (string) Str::uuid(),
                'preset' => 'concern_raised',
                'is_enabled' => false,
                'configuration_id' => null,
                'configuration_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('alert_configurations')
                ->where('preset', 'concern_raised')
                ->delete();
        });
    }
};
