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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        $this->renameCaseInsensitiveDuplicates(
            table: 'customer_advisor_categories',
            ownerColumn: 'customer_advisor_id',
        );

        $this->renameCaseInsensitiveDuplicates(
            table: 'employee_advisor_categories',
            ownerColumn: 'employee_advisor_id',
        );
    }

    public function down(): void {}

    private function renameCaseInsensitiveDuplicates(string $table, string $ownerColumn): void
    {
        $records = DB::table($table)
            ->select(['id', $ownerColumn, 'name'])
            ->orderBy($ownerColumn)
            ->orderByRaw('LOWER(name)')
            ->orderBy('id')
            ->get();

        /** @var array<string, array<string, bool>> $seenByOwner */
        $seenByOwner = [];

        foreach ($records as $record) {
            $ownerId = (string) $record->{$ownerColumn};
            $normalizedName = Str::lower((string) $record->name);

            if (! array_key_exists($ownerId, $seenByOwner)) {
                $seenByOwner[$ownerId] = [];
            }

            if (! isset($seenByOwner[$ownerId][$normalizedName])) {
                $seenByOwner[$ownerId][$normalizedName] = true;

                continue;
            }

            $uniqueName = $this->generateUniqueName(
                table: $table,
                ownerColumn: $ownerColumn,
                ownerId: $ownerId,
                originalName: (string) $record->name,
                recordId: (string) $record->id,
            );

            DB::table($table)
                ->where('id', $record->id)
                ->update([
                    'name' => $uniqueName,
                    'updated_at' => now(),
                ]);

            $seenByOwner[$ownerId][Str::lower($uniqueName)] = true;
        }
    }

    private function generateUniqueName(
        string $table,
        string $ownerColumn,
        string $ownerId,
        string $originalName,
        string $recordId,
    ): string {
        $baseName = trim($originalName);

        if ($baseName === '') {
            $baseName = 'Category';
        }

        $suffixCounter = 2;

        while (true) {
            $suffix = ' (' . $suffixCounter . ')';
            $maxLength = 255 - strlen($suffix);
            $candidateBase = Str::substr($baseName, 0, max(1, $maxLength));
            $candidate = $candidateBase . $suffix;

            $exists = DB::table($table)
                ->where($ownerColumn, $ownerId)
                ->where('id', '<>', $recordId)
                ->whereRaw('LOWER(name) = ?', [Str::lower($candidate)])
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $suffixCounter++;
        }
    }
};
