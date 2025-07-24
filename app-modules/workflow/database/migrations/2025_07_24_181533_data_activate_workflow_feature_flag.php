<?php

use App\Features\WorkflowFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        WorkflowFeature::activate();
    }

    public function down(): void
    {
        WorkflowFeature::deactivate();
    }
};
