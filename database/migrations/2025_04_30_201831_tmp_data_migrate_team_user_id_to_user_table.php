<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('team_user')->chunkById(100, function ($teamUser) {
            DB::table('users')
                ->where('id', $teamUser->user_id)  // @phpstan-ignore-line
                ->update(['team_id' => $teamUser->team_id]);  // @phpstan-ignore-line
        });
    }

    public function down(): void
    {
        DB::table('users')->chunkById(100, function ($teamUser) {
            DB::table('team_user')->insert([
                'team_id' => $teamUser->team_id,  // @phpstan-ignore-line
                'user_id' => $teamUser->user_id,  // @phpstan-ignore-line
                'updated_at' => $teamUser->updated_at,  // @phpstan-ignore-line
                'created_at' => $teamUser->created_at,  // @phpstan-ignore-line
            ]);
        });
    }
};
