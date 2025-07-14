<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE workflow_steps RENAME COLUMN details_id TO current_details_id');
        
        DB::statement('ALTER TABLE workflow_steps RENAME COLUMN details_type TO current_details_type');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE workflow_steps RENAME COLUMN current_details_id TO details_id');
        
        DB::statement('ALTER TABLE workflow_steps RENAME COLUMN current_details_type TO details_type');

        DB::commit();
    }
};
