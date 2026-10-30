<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Database\Seeders;

use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\ProspectStatus;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProspectStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'classification' => SystemProspectClassification::New,
                'name' => 'New',
                'color' => Color::Sky->value,
                'is_system_protected' => true,
            ],
            [
                'classification' => SystemProspectClassification::Assigned,
                'name' => 'Assigned',
                'color' => Color::Yellow->value,
            ],
            [
                'classification' => SystemProspectClassification::InProgress,
                'name' => 'In-Progress',
                'color' => Color::Blue->value,
            ],
            [
                'classification' => SystemProspectClassification::Converted,
                'name' => 'Converted',
                'color' => Color::Green->value,
                'is_system_protected' => true,
            ],
            [
                'classification' => SystemProspectClassification::Recycled,
                'name' => 'Recycled',
                'color' => Color::Gray->value,
            ],
            [
                'classification' => SystemProspectClassification::NotInterested,
                'name' => 'Not Interested',
                'color' => Color::Red->value,
            ],
        ];

        foreach ($statuses as $status) {
            $desiredClassification = $status['classification'];
            $desiredProtection = $status['is_system_protected'] ?? false;

            $existingStatus = ProspectStatus::query()->where('name', $status['name'])->first();

            if ($existingStatus === null) {
                ProspectStatus::factory()->create($status);

                continue;
            }

            // An earlier data migration provisions "New" and "Converted" with the default
            // is_system_protected = false. Reconcile existing rows to the required
            // classification and protection so lookups in ConvertToStudent and
            // DisassociateStudent (name + is_system_protected) keep working, instead of
            // skipping them and leaving a non-protected default behind.
            if (
                $existingStatus->classification === $desiredClassification
                && $existingStatus->is_system_protected === $desiredProtection
            ) {
                continue;
            }

            // Use a direct update to bypass the is_system_protected mass-assignment guard.
            // The modification-prevention trigger only blocks already-protected rows, so
            // promoting a currently non-protected default to protected is permitted.
            DB::table('prospect_statuses')
                ->where('id', $existingStatus->getKey())
                ->update([
                    'classification' => $desiredClassification->value,
                    'is_system_protected' => $desiredProtection,
                ]);
        }
    }
}
