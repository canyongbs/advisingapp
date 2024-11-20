<?php

use App\Features\CaseManagement;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CaseManagement::activate();
    }

    public function down(): void
    {
        CaseManagement::deactivate();
    }
};
