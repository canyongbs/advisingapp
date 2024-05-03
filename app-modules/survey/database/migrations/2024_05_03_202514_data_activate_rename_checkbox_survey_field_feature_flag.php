<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('introduce-checkboxes-form-field');
    }

    public function down(): void
    {
        Feature::deactivate('introduce-checkboxes-form-field');
    }
};
