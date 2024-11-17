<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $alerts = DB::table('alerts')->whereNotNull('status')->get();

        $alerts->each(function ($alert, $key) {
            $status = DB::table('alert_statuses')->where('classification', $alert->status)->first();
            DB::table('alerts')->where('id', $alert->id)->update(['status_id' => $status->id]);
        });
    }

    public function down(): void
    {
        Schema::table('alert', function (Blueprint $table) {
            //
        });
    }
};
