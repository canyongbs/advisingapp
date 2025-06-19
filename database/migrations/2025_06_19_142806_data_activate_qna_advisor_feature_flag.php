<?php

use App\Features\QnAAdvisorFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        QnAAdvisorFeature::activate();
    }

    public function down(): void
    {
        QnAAdvisorFeature::deactivate();
    }
};
