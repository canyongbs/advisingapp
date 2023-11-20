<?php

namespace Assist\Alert\Database\Seeders;

use Assist\Alert\Models\Alert;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        Alert::factory()
            ->count(50)
            ->create();
    }
}
