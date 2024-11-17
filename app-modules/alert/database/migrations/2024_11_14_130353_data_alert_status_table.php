<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $activeStatus = DB::table('alert_statuses')
            ->where('classification', 'active')
            ->where('name', 'Active')
            ->first();

        if ($activeStatus === null) {
            if (! app()->runningUnitTests()) {
                DB::table('alert_statuses')->insert([
                    'id' => (string) Str::orderedUuid(),
                    'classification' => 'active',
                    'name' => 'active',
                    'created_at' => now(),
                    'is_default' => true,
                    'sort' => DB::raw('(SELECT COALESCE(MAX(alert_statuses.sort), 0) + 1 FROM alert_statuses)'),
                ]);
            }
        }

        $resolvedStatus = DB::table('alert_statuses')
            ->where('classification', 'resolved')
            ->where('name', 'Resolved')
            ->first();

        if ($resolvedStatus === null) {
            if (! app()->runningUnitTests()) {
                DB::table('alert_statuses')->insert([
                    'id' => (string) Str::orderedUuid(),
                    'classification' => 'resolved',
                    'name' => 'resolved',
                    'created_at' => now(),
                    'sort' => DB::raw('(SELECT COALESCE(MAX(alert_statuses.sort), 0) + 1 FROM alert_statuses)'),
                ]);
            }
        }

        $canceledStatus = DB::table('alert_statuses')
            ->where('classification', 'canceled')
            ->where('name', 'Canceled')
            ->first();

        if ($canceledStatus === null) {
            if (! app()->runningUnitTests()) {
                DB::table('alert_statuses')->insert([
                    'id' => (string) Str::orderedUuid(),
                    'classification' => 'canceled',
                    'name' => 'canceled',
                    'created_at' => now(),
                    'sort' => DB::raw('(SELECT COALESCE(MAX(alert_statuses.sort), 0) + 1 FROM alert_statuses)'),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
