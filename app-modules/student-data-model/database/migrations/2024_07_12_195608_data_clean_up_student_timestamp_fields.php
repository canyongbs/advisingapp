<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::purge('student_timestamp_fields');
    }

    public function down(): void
    {
        Feature::activate('student_timestamp_fields');
    }
};
