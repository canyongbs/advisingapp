<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('email_messages')
            ->where('recipient_type', 'anonymous')
            ->delete();
    }

    public function down(): void
    {
        // There is no way to undo this operation
    }
};
