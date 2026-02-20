<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use App\Features\ProspectStatusFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE prospect_statuses DISABLE TRIGGER prevent_modification_of_system_protected_rows');

            DB::table('prospect_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $newColor = match ($status->color) {
                        'success' => 'green',
                        'danger' => 'red',
                        'warning' => 'yellow',
                        'info' => 'sky',
                        'primary' => 'blue',
                        'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($newColor !== $status->color) {
                        DB::table('prospect_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $newColor]);
                    }
                }
            });

            DB::statement('ALTER TABLE prospect_statuses ENABLE TRIGGER prevent_modification_of_system_protected_rows');

            ProspectStatusFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ProspectStatusFeature::deactivate();

            DB::statement('ALTER TABLE prospect_statuses DISABLE TRIGGER prevent_modification_of_system_protected_rows');

            DB::table('prospect_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $oldColor = match ($status->color) {
                        'green' => 'success',
                        'red' => 'danger',
                        'yellow' => 'warning',
                        'sky' => 'info',
                        'blue' => 'primary',
                        'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($oldColor !== $status->color) {
                        DB::table('prospect_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $oldColor]);
                    }
                }
            });

            DB::statement('ALTER TABLE prospect_statuses ENABLE TRIGGER prevent_modification_of_system_protected_rows');
        });
    }
};
