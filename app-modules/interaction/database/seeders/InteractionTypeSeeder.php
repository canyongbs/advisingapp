<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionType;

class InteractionTypeSeeder extends Seeder
{
    public function run(): void
    {
        InteractionType::factory()
            ->count(10)
            ->create();
    }
}
