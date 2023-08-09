<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\ProspectSource;

class ProspectSourceSeeder extends Seeder
{
    public function run(): void
    {
        ProspectSource::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Online'],
                ['name' => 'Phone'],
                ['name' => 'Social Media'],
                ['name' => 'Walk-In'],
                ['name' => 'Local Ad'],
            )
            ->create();
    }
}
