<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\CaseItemPriority;

class CaseItemPrioritySeeder extends Seeder
{
    public function run(): void
    {
        CaseItemPriority::factory()
            ->count(3)
            ->sequence(
                ['name' => 'High', 'order' => 1],
                ['name' => 'Medium', 'order' => 2],
                ['name' => 'Low', 'order' => 3],
            )
            ->create();
    }
}
