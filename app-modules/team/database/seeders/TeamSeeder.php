<?php

namespace Assist\Team\Database\Seeders;

use App\Models\User;
use Assist\Team\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        Team::factory()
            ->count(10)
            ->afterCreating(function (Team $team) {
                $users = collect(User::inRandomOrder()->take(3)->get() ?? User::factory()->create());

                $users->each(fn (User $user) => $team->users()->attach($user));
            })
            ->create();
    }
}
