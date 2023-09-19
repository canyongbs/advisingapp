<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InternalUsersSeeder extends Seeder
{
    public function run(): void
    {
        collect(config('internal-users.emails'))->each(function ($email) {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                User::factory()->create([
                    'name' => Str::title(Str::replace('.', ' ', Str::before($email, '@'))),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_external' => true,
                ]);
            }
        });
    }
}
