<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $statuses = DB::table('alert_statuses')->select('id', 'classification')->get()->toArray();
        DB::table('histories')->where('subject_type', 'alert')->eachById(function ($alertHistory, $key) use ($statuses) {
            $statusId = '';
            $alertData = json_decode($alertHistory->new);

            foreach ($statuses as $status) {
                if (isset($alertData->status) && $alertData->status == $status->classification) {
                    $statusId = $status->id;
                }
            }

            unset($alertData->status);
            $alertData->status_id = $statusId;

            DB::table('histories')->where('id', $alertHistory->id)->update(['new' => json_encode($alertData)]);
        });
    }

    public function down(): void {}
};
