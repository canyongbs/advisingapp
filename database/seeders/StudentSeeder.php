<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\AssistDataModel\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()
            ->create([
                'full_name' => 'Twilio Tester',
                'mobile' => config('services.twilio.from_number'),
            ]);
    }
}
