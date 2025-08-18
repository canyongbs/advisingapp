<?php

use AdvisingApp\Project\Database\Seeders\ProjectMilestoneStatusSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class () extends Migration {
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => ProjectMilestoneStatusSeeder::class,
            '--force' => true,
        ]);
    }

    public function down(): void {}
};
