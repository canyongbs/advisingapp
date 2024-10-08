<?php

use App\Features\ProspectStatusSystemProtectionAndAutoAssignment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ProspectStatusSystemProtectionAndAutoAssignment::activate();
    }

    public function down(): void
    {
        ProspectStatusSystemProtectionAndAutoAssignment::deactivate();
    }
};
