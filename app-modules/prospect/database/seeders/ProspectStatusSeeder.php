<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Enums\ProspectStatusColorOptions;

class ProspectStatusSeeder extends Seeder
{
    public function run(): void
    {
        ProspectStatus::factory()
            ->createMany(
                [
                    [
                        'name' => 'New',
                        'color' => ProspectStatusColorOptions::INFO->value,
                    ],
                    [
                        'name' => 'Assigned',
                        'color' => ProspectStatusColorOptions::WARNING->value,
                    ],
                    [
                        'name' => 'In-Progress',
                        'color' => ProspectStatusColorOptions::PRIMARY->value,
                    ],
                    [
                        'name' => 'Converted',
                        'color' => ProspectStatusColorOptions::SUCCESS->value,
                    ],
                    [
                        'name' => 'Recycled',
                        'color' => ProspectStatusColorOptions::GRAY->value,
                    ],
                    [
                        'name' => 'Dead',
                        'color' => ProspectStatusColorOptions::DANGER->value,
                    ],
                ]
            );
    }
}
