<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE customer_advisor_categories ALTER COLUMN name TYPE citext');
            DB::statement('ALTER TABLE employee_advisor_categories ALTER COLUMN name TYPE citext');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE customer_advisor_categories ALTER COLUMN name TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE employee_advisor_categories ALTER COLUMN name TYPE VARCHAR(255)');
        });
    }
};
