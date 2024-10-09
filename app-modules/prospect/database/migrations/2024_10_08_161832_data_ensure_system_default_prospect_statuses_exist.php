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

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $newStatus = DB::table('prospect_statuses')
            ->where('classification', 'new')
            ->where('name', 'New')
            ->first();

        if ($newStatus === null) {
            if (! app()->runningUnitTests()) {
                DB::table('prospect_statuses')->insert([
                    'id' => (string) Str::orderedUuid(),
                    'classification' => 'new',
                    'name' => 'New',
                    'color' => 'info',
                    'created_at' => now(),
                    'sort' => DB::raw('(SELECT COALESCE(MAX(prospect_statuses.sort), 0) + 1 FROM prospect_statuses)'),
                ]);
            }
        } else {
            if ($newStatus->is_system_protected !== true) {
                DB::table('prospect_statuses')
                    ->where('id', $newStatus->id)
                    ->update([
                        'is_system_protected' => true,
                    ]);
            }
        }

        $convertedStatus = DB::table('prospect_statuses')
            ->where('classification', 'converted')
            ->where('name', 'Converted')
            ->first();

        if ($convertedStatus === null) {
            if (! app()->runningUnitTests()) {
                DB::table('prospect_statuses')->insert([
                    'id' => (string) Str::orderedUuid(),
                    'classification' => 'converted',
                    'name' => 'Converted',
                    'color' => 'success',
                    'created_at' => now(),
                    'sort' => DB::raw('(SELECT COALESCE(MAX(prospect_statuses.sort), 0) + 1 FROM prospect_statuses)'),
                ]);
            }
        } else {
            if ($convertedStatus->is_system_protected !== true) {
                DB::table('prospect_statuses')
                    ->where('id', $convertedStatus->id)
                    ->update([
                        'is_system_protected' => true,
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('prospect_statuses')
            ->where('classification', 'new')
            ->where('name', 'New')
            ->update([
                'is_system_protected' => false,
            ]);

        DB::table('prospect_statuses')
            ->where('classification', 'converted')
            ->where('name', 'Converted')
            ->update([
                'is_system_protected' => false,
            ]);
    }
};
