<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
return new class extends Migration
{
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

    public function down(): void
    {}

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
