<?php

namespace Database\Seeders;

use App\Models\CaseItemStatus;
use Illuminate\Database\Seeder;

class CaseItemStatusSeeder extends Seeder
{
    public function run(): void
    {
        CaseItemStatus::factory()
            ->open()
            ->create();

        CaseItemStatus::factory()
            ->in_progress()
            ->create();

        CaseItemStatus::factory()
            ->closed()
            ->create();
    }
}
