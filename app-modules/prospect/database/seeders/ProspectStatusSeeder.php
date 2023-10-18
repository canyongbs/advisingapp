<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Enums\ProspectStatusColorOptions;
use Assist\Prospect\Enums\SystemProspectClassification;

class ProspectStatusSeeder extends Seeder
{
    public function run(): void
    {
        ProspectStatus::factory()
            ->createMany(
                [
                    [
                        'classification' => SystemProspectClassification::New,
                        'name' => 'New',
                        'color' => ProspectStatusColorOptions::Info->value,
                    ],
                    [
                        'classification' => SystemProspectClassification::Assigned,
                        'name' => 'Assigned',
                        'color' => ProspectStatusColorOptions::Warning->value,
                    ],
                    [
                        'classification' => SystemProspectClassification::InProgress,
                        'name' => 'In-Progress',
                        'color' => ProspectStatusColorOptions::Primary->value,
                    ],
                    [
                        'classification' => SystemProspectClassification::Converted,
                        'name' => 'Converted',
                        'color' => ProspectStatusColorOptions::Success->value,
                    ],
                    [
                        'classification' => SystemProspectClassification::Recycled,
                        'name' => 'Recycled',
                        'color' => ProspectStatusColorOptions::Gray->value,
                    ],
                    [
                        'classification' => SystemProspectClassification::Dead,
                        'name' => 'Dead',
                        'color' => ProspectStatusColorOptions::Danger->value,
                    ],
                ]
            );
    }
}
