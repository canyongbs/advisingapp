<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class () extends Migration {
    public function up(): void
    {
        // Workers and the autoscale manager booted before the flag was activated keep queue monitoring off until they restart.
        Artisan::call('queue:restart');
    }

    public function down(): void {}
};
