<?php

namespace Assist\Division\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Division\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Division::factory()
            ->createMany(
                [
                    [
                        'name' => 'Home',
                        'code' => 'home',
                        'description' => 'Home Division',
                        'header' => null,
                        'footer' => null,
                    ],
                ]
            );
    }
}
