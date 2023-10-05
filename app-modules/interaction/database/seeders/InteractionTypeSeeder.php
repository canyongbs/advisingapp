<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionType;

class InteractionTypeSeeder extends Seeder
{
    public function run(): void
    {
        InteractionType::factory()
            ->createMany(
                [
                    ['name' => 'Phone'],
                    ['name' => 'Live Chat'],
                    ['name' => 'SMS (Outside ASSIST)'],
                    ['name' => 'In-Person'],
                    ['name' => 'Virtual Meeting'],
                    ['name' => 'Email (Outside ASSIST)'],
                    ['name' => 'Postal Mail'],
                ]
            );
    }
}
