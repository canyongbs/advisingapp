<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('rename-checkbox-form-field');
    }

    public function down(): void
    {
        Feature::deactivate('rename-checkbox-form-field');
    }
};
