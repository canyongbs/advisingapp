<?php

use Illuminate\Database\Migrations\Migration;
use App\Features\ProspectStatusSystemProtectionAndAutoAssignment;

return new class () extends Migration {
    public function up(): void
    {
        ProspectStatusSystemProtectionAndAutoAssignment::activate();
    }

    public function down(): void
    {
        ProspectStatusSystemProtectionAndAutoAssignment::deactivate();
    }
};
