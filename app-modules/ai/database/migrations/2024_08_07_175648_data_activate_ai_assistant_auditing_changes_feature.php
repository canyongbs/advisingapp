<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('ai-assistant-auditing-changes');
    }

    public function down(): void
    {
        Feature::purge('ai-assistant-auditing-changes');
    }
};
