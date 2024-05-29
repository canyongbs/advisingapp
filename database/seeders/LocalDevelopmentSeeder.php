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

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Integration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Enums\LicenseType;

class LocalDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isLocal()) {
            $this->internalAdminUsers();
            $this->prospects();
            $this->twilio();
        }
    }

    private function internalAdminUsers(): void
    {
        $superAdminRole = Role::where('name', 'authorization.super_admin')->first();

        if (! $superAdminRole) {
            return;
        }

        collect(config('local_development.users.emails'))->each(function ($email) use ($superAdminRole) {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                $user = User::factory()
                    ->licensed(LicenseType::cases())
                    ->create([
                        'name' => str($email)->replace('.', ' ')->before('@')->title(),
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'is_external' => true,
                    ]);
            }

            $user->roles()->sync($superAdminRole);
        });
    }

    private function prospects(): void
    {
        collect(config('local_development.prospects.emails'))->each(function ($email) {
            $contact = Prospect::where('email', $email)->first();

            if (is_null($contact)) {
                $name = str($email)->replace('.', ' ')->before('@')->title();

                Prospect::factory()->create([
                    'full_name' => $name,
                    'first_name' => $name->before(' '),
                    'last_name' => $name->after(' '),
                    'email' => $email,
                ]);
            }
        });
    }

    private function twilio(): void
    {
        $twilio = Integration::Twilio->settings();

        $twilio->account_sid = config('local_development.twilio.account_sid');
        $twilio->auth_token = config('local_development.twilio.auth_token');
        $twilio->from_number = config('local_development.twilio.from_number');
        $twilio->save();

        if ($twilio->isConfigured()) {
            $twilio->is_enabled = true;
            $twilio->save();
        }
    }
}
