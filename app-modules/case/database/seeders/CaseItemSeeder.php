<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\CaseItem;

class CaseItemSeeder extends Seeder
{
    public function run(): void
    {
        CaseItem::factory()
            ->count(30)
            ->create();
    }
}
