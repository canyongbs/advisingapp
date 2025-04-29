<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use App\Enums\Integration;
use App\Models\Authenticatable;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LocalDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isLocal()) {
            $this->internalAdminUsers();
            $this->twilio();
        }
    }

    private function internalAdminUsers(): void
    {
        $superAdminRole = Role::where('name', Authenticatable::SUPER_ADMIN_ROLE)->first();

        if (! $superAdminRole) {
            return;
        }

        /** @var array<string> $emails */
        $emails = config('local_development.internal_users.emails');
        collect($emails)->each(function ($email) use ($superAdminRole) {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                $user = User::factory()->create([
                    'name' => Str::title(Str::replace('.', ' ', Str::before($email, '@'))),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_external' => true,
                ]);
            }

            $user->roles()->sync($superAdminRole);
        });
    }

    private function twilio(): void
    {
        $twilio = Integration::Twilio->settings();
        throw_unless($twilio instanceof TwilioSettings, new Exception('The Twilio settings object must be an instance of [TwilioSettings].'));

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
