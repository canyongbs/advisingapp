<?php

use App\Features\StudentGender;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        StudentGender::activate();
    }

    public function down(): void
    {
        StudentGender::deactivate();
    }
};
