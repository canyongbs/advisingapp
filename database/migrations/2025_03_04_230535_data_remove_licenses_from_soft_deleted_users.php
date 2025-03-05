<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('users')->whereNotNull('deleted_at')->get();

        if ($users) {
            foreach ($users as $user) {
                DB::table('licenses')->where('user_id', $user->id)->delete();
            }
        }

    }
};
