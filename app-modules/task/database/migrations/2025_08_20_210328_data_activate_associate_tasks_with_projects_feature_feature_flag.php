<?php

use App\Features\AssociateTasksWithProjectsFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AssociateTasksWithProjectsFeature::activate();
    }

    public function down(): void
    {
        AssociateTasksWithProjectsFeature::deactivate();
    }
};
