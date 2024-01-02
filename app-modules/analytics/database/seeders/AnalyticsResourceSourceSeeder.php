<?php

namespace AdvisingApp\Analytics\Database\Seeders;

use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalyticsResourceSourceSeeder extends Seeder
{
    public function run(): void
    {
        AnalyticsResourceSource::factory()
            ->createMany([
                ['name' => 'Power BI'],
                ['name' => 'Tableau'],
                ['name' => 'Spreadsheet'],
            ]);
    }
}
