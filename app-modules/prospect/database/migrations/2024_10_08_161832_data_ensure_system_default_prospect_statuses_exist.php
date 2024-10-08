<?php

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
                    'sort' => DB::raw('(SELECT MAX(sort) + 1 FROM prospect_statuses)'),
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
                    'sort' => DB::raw('(SELECT MAX(sort) + 1 FROM prospect_statuses)'),
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
