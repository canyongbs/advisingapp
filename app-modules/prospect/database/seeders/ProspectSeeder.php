<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\Prospect;

class ProspectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prospect::factory()
            ->count(25)
            ->create();
    }
}
