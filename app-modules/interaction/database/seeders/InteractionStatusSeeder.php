<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionStatus;

class InteractionStatusSeeder extends Seeder
{
    public function run(): void
    {
        InteractionStatus::factory()
            ->createMany(
                [
                    ['name' => 'Planned'],
                    ['name' => 'Held'],
                    ['name' => 'Not Held'],
                ]
            );
    }
}
