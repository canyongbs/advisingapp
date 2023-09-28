<?php

namespace Assist\Team\Database\Seeders;

use App\Models\User;
use Assist\Team\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::factory()
            ->count(10)
            ->create();

        User::all()
            ->each(fn (User $user) => $user->team()->associate($teams->random())->save());
    }
}
