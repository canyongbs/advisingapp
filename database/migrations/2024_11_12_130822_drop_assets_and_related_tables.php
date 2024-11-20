<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('asset_check_outs');
        Schema::dropIfExists('asset_check_ins');
        Schema::dropIfExists('maintenance_activities');
        Schema::dropIfExists('maintenance_providers');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_locations');
        Schema::dropIfExists('asset_statuses');
        Schema::dropIfExists('asset_types');
    }

    public function down(): void {}
};
