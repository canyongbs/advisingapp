<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\StudentDataModel\Models\Student;

class TwilioStudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()
            ->create([
                'full_name' => 'Twilio Tester',
                'mobile' => config('services.twilio.test_from_number'),
            ]);
    }
}
