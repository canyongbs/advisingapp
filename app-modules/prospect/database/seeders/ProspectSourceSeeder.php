<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\ProspectSource;

class ProspectSourceSeeder extends Seeder
{
    public function run(): void
    {
        ProspectSource::factory()
            ->createMany(
                [
                    ['name' => 'ASSIST'],
                    ['name' => 'Import'],
                ]
            );
    }
}
