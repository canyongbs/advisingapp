<?php

use Laravel\Pennant\Feature;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        if (Feature::active('change-agent-name')) {
            try {
                DB::beginTransaction();

                $audits = DB::table('audits')
                    ->join('users', 'audits.change_agent_id', '=', 'users.id')
                    ->select('audits.id', 'users.name')
                    ->get();

                foreach ($audits as $audit) {
                    DB::table('audits')
                        ->where('id', $audit->id)
                        ->update(['change_agent_name' => $audit->name]);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                throw new Exception($e);
            }
        }
    }

    public function down(): void
    {
        if (Feature::active('change-agent-name')) {
            try {
                DB::table('audits')
                    ->update(['change_agent_name' => null]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
};
