<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionDriver;

class InteractionDriverSeeder extends Seeder
{
    public function run(): void
    {
        InteractionDriver::factory()
            ->count(10)
            ->create();
    }
}
