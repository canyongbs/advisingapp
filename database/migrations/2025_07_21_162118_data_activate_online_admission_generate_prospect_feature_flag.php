<?php

use App\Features\OnlineAdmissionGenerateProspect;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        OnlineAdmissionGenerateProspect::activate();
    }

    public function down(): void
    {
        OnlineAdmissionGenerateProspect::deactivate();
    }
};
