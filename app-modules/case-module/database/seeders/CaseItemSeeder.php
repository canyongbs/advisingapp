<?php

namespace Assist\CaseModule\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\CaseModule\Models\CaseItem;

class CaseItemSeeder extends Seeder
{
    public function run(): void
    {
        CaseItem::factory()
            ->count(30)
            ->create();
    }
}
