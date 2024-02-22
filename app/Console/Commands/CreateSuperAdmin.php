<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use AdvisingApp\Authorization\Models\Role;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin {--tenant=}';

    protected $description = 'Creates a super admin user.';

    public function handle(): void
    {
        if (config('app.allow_super_admin_creation') === false) {
            $this->error('Super admin creation is disabled.');

            return;
        }

        $tenant = Tenant::find($this->option('tenant') ?? $this->ask('Enter tenant ID'));

        if (is_null($tenant)) {
            $this->error('Tenant not found.');

            return;
        }

        $tenant->makeCurrent();

        $this->comment('Creating super admin user...');

        $validator = Validator::make([
            'email' => $this->ask('Enter the email address for the super admin user'),
            'name' => $this->ask('Enter the name for the super admin user'),
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();

        $password = Str::random(24);

        $user = User::create(
            [
                'name' => $validator->validated()['name'],
                'email' => $validator->validated()['email'],
                'password' => Hash::make($password),
            ]
        );

        $user->assignRole(Role::superAdmin()->get());

        $this->info('Super admin user created successfully.');

        $this->info("Email: {$validator->validated()['email']}");

        $this->info("Password: {$password}");
    }
}
