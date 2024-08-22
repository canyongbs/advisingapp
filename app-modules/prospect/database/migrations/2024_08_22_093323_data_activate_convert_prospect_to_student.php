<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('convert_prospect_to_student');
    }

    public function down(): void
    {
        Feature::deactivate('convert_prospect_to_student');
    }
};
