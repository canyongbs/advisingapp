<?php

namespace Database\Seeders;

use App\Models\CaseItem;
use Illuminate\Database\Seeder;

class CaseItemSeeder extends Seeder
{
    public function run(): void
    {
        CaseItem::factory()
            ->count(30)
            ->create();
    }
}
