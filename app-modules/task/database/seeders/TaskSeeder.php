<?php

namespace Assist\Task\Database\Seeders;

use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Past due, Student Concerned
        Task::factory()
            ->count(3)
            ->assigned(User::find(1))
            ->concerningStudent()
            ->pastDue()
            ->create();

        // Past due, Prospect Concerned
        Task::factory()
            ->count(3)
            ->assigned(User::find(1))
            ->concerningProspect()
            ->pastDue()
            ->create();

        // Due Later, Student Concerned
        Task::factory()
            ->count(3)
            ->assigned(User::find(1))
            ->concerningStudent()
            ->dueLater()
            ->create();

        // Due Later, Prospect Concerned
        Task::factory()
            ->count(3)
            ->assigned(User::find(1))
            ->concerningProspect()
            ->dueLater()
            ->create();

        // Unassigned
        Task::factory()
            ->count(3)
            ->concerningStudent()
            ->create();

        // Unassigned, Past Due
        Task::factory()
            ->count(3)
            ->concerningStudent()
            ->pastDue()
            ->create();
    }
}
