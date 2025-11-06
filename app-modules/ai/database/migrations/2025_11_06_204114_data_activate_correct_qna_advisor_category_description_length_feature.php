<?php

use App\Features\CorrectQnaAdvisorCategoryDescriptionLengthFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        CorrectQnaAdvisorCategoryDescriptionLengthFeature::activate();
    }

    public function down(): void
    {
        CorrectQnaAdvisorCategoryDescriptionLengthFeature::deactivate();
    }
};
