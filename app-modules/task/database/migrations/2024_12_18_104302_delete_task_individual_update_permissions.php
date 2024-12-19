<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('permissions')
            ->select('id')
            ->whereRaw("name ~ '^task\\.[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}\\.update$'")
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                $ids = $rows->pluck('id')->toArray();
                DB::table('permissions')->whereIn('id', $ids)->delete();
            });
    }
};
