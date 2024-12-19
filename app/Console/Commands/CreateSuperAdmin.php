<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Console\Commands;

use AdvisingApp\Authorization\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin {--tenant=}';

    protected $description = 'Creates a super admin user.';

    public function handle(): int
    {
        if (config('app.allow_super_admin_creation') === false) {
            $this->error('Super admin creation is disabled.');

            return static::FAILURE;
        }

        $tenant = Tenant::find($this->option('tenant') ?? $this->ask('Enter tenant ID'));

        if (is_null($tenant)) {
            $this->error('Tenant not found.');

            return static::FAILURE;
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

        $user = User::create([
            'name' => $validator->validated()['name'],
            'email' => $validator->validated()['email'],
            'password' => Hash::make($password),
        ]);

        $user->assignRole(Role::superAdmin()->get());

        $this->info('Super admin user created successfully.');

        $this->info("Email: {$validator->validated()['email']}");

        $this->info("Password: {$password}");

        return static::SUCCESS;
    }
}
