<?php

namespace Assist\Form\Database\Seeders;

use Assist\Form\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form::factory()->count(10)->create();
    }
}
