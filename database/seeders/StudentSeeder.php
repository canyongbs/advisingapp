<?php

namespace Database\Seeders;

use Assist\AssistDataModelModule\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()
            ->count(10)
            ->create();
    }
}
