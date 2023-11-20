<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionRelation;

class InteractionRelationSeeder extends Seeder
{
    public function run(): void
    {
        InteractionRelation::factory()
            ->createMany(
                [
                    ['name' => 'Self'],
                    ['name' => 'Parent'],
                    ['name' => 'Spouse'],
                    ['name' => 'Sibling'],
                    ['name' => 'Other'],
                ]
            );
    }
}
