<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseUpdate;

class CaseUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CaseItem::each(function (CaseItem $case) {
            CaseUpdate::factory()
                ->count(3)
                ->for($case, 'case')
                ->create();
        });
    }
}
