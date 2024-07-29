<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        try {
            DB::beginTransaction();

            DB::table('audits')
                ->join('users', 'audits.change_agent_id', '=', 'users.id')
                ->select('audits.id', 'users.name')
                ->chunkById(100, function (Collection $audits) {
                    foreach ($audits as $audit) {
                        DB::table('audits')
                            ->where('audits.id', $audit->id)
                            ->update(['change_agent_name' => $audit->name]);
                    }
                }, 'audits.id', 'id');

            DB::commit();
        } catch (Exception $error) {
            DB::rollBack();

            throw $error;
        }
    }

    public function down(): void
    {
        try {
            DB::table('audits')
                ->update(['change_agent_name' => null]);
        } catch (Exception $error) {
            throw $error;
        }
    }
};
