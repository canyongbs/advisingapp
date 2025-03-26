<?php

use App\Features\CareTeamRoleFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        CareTeamRoleFeature::activate();
    }

    public function down(): void
    {
        CareTeamRoleFeature::deactivate();
    }
};
