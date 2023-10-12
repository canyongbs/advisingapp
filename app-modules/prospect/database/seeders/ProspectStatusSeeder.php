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
                        'color' => ProspectStatusColorOptions::Info->value,
                    ],
                    [
                        'name' => 'Assigned',
                        'color' => ProspectStatusColorOptions::Warning->value,
                    ],
                    [
                        'name' => 'In-Progress',
                        'color' => ProspectStatusColorOptions::Primary->value,
                    ],
                    [
                        'name' => 'Converted',
                        'color' => ProspectStatusColorOptions::Success->value,
                    ],
                    [
                        'name' => 'Recycled',
                        'color' => ProspectStatusColorOptions::Gray->value,
                    ],
                    [
                        'name' => 'Dead',
                        'color' => ProspectStatusColorOptions::Danger->value,
                    ],
                ]
            );
    }
}
