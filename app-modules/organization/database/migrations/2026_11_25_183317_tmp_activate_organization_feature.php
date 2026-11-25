<?php

use App\Features\OrganizationFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        OrganizationFeature::activate();
    }

    public function down(): void
    {
        OrganizationFeature::deactivate();
    }
};
