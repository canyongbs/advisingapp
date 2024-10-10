<?php

use App\Features\ManageStudentConfigurationFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ManageStudentConfigurationFeature::activate();
    }

    public function down(): void
    {
        ManageStudentConfigurationFeature::deactivate();
    }
};
