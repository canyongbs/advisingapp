<?php

use App\Features\PastSubmissionsFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        PastSubmissionsFeature::activate();
    }

    public function down(): void
    {
        PastSubmissionsFeature::deactivate();
    }
};
