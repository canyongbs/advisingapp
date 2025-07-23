<?php

use App\Features\OnlineAdmissionGenerateProspect;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        OnlineAdmissionGenerateProspect::activate();
    }

    public function down(): void
    {
        OnlineAdmissionGenerateProspect::deactivate();
    }
};
