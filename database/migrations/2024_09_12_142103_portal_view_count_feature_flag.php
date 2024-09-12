<?php

use Illuminate\Database\Migrations\Migration;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('portal_view_count');
    }

    public function down(): void
    {
        Feature::deactivate('portal_view_count');
    }
};
