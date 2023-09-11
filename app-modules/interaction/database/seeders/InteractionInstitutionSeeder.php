<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionInstitution;

class InteractionInstitutionSeeder extends Seeder
{
    public function run(): void
    {
        InteractionInstitution::factory()
            ->count(10)
            ->create();
    }
}
