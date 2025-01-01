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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        $role_details = DB::table('roles')->where('name', 'analytics.analytics_management')->whereIn('guard_name', ['web', 'api'])->get();

        if (! $role_details->isEmpty()) {
            foreach ($role_details as $role) {
                DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
                DB::table('roles')->where('id', $role->id)->delete();
            }
        }
    }

    public function down(): void
    {
        $analytics_roles = [
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'analytics.analytics_management',
                'guard_name' => 'web',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'analytics.analytics_management',
                'guard_name' => 'api',
                'created_at' => now(),
            ],
        ];
        DB::table('roles')->insert($analytics_roles);
    }
};
