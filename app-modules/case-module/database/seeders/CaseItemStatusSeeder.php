<?php

namespace Assist\CaseModule\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\CaseModule\Models\CaseItemStatus;

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
