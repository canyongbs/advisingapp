<?php

namespace Assist\Prospect\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Enums\ProspectStatusColorOptions;

class ProspectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProspectStatus::factory()
            ->create(
                [
                    'name' => 'New',
                    'color' => ProspectStatusColorOptions::INFO->value,
                ]
            );

        ProspectStatus::factory()
            ->create(
                [
                    'name' => 'Contacted',
                    'color' => ProspectStatusColorOptions::SUCCESS->value,
                ]
            );

        ProspectStatus::factory()
            ->create(
                [
                    'name' => 'Not Interested',
                    'color' => ProspectStatusColorOptions::DANGER->value,
                ]
            );

        ProspectStatus::factory()
            ->create(
                [
                    'name' => 'Interested',
                    'color' => ProspectStatusColorOptions::WARNING->value,
                ]
            );
    }
}
