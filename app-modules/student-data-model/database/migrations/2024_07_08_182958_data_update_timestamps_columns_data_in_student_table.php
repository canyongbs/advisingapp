<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::yesterday();

        $students = DB::table('students')->get();
        $students->each(function ($student, $key) use ($startDate, $endDate) {
            $randomDateTime = Carbon::createFromTimestamp(mt_rand($startDate->timestamp, $endDate->timestamp))->format('Y-m-d H:i:s');
            DB::table('students')
                ->where('sisid', $student->sisid)
                ->update([
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_at_source' => $randomDateTime,
                    'updated_at_source' => $randomDateTime,
                ]);
        });
    }

    public function down(): void
    {
        $data = [
            'created_at' => null,
            'updated_at' => null,
            'created_at_source' => null,
            'updated_at_source' => null,
        ];
        $students = DB::table('students')->get();
        $students->each(function ($student, $key) use ($data) {
            DB::table('students')->where('sisid', $student->sisid)->update($data);
        });
    }
};
