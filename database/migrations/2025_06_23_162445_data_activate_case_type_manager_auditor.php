<?php

use App\Features\CaseTypeManagerAuditor;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CaseTypeManagerAuditor::activate();
    }

    public function down(): void
    {
        CaseTypeManagerAuditor::deactivate();
    }
};
