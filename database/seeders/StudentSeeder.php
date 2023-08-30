<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\AssistDataModel\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()
            ->count(10)
            ->create();

        Student::factory()
            ->create([
                'mobile' => config('services.twilio.from_number'),
            ]);
    }
}
