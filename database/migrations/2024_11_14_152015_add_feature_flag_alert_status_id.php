<?php

use App\Features\AlertStatusId;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AlertStatusId::activate();
    }

    public function down(): void
    {
        AlertStatusId::deactivate();
    }
};
