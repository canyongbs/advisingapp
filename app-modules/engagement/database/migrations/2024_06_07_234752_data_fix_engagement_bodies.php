<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('engagements')
            ->whereNotNull('body')
            ->whereJsonDoesntContainKey('body->type')
            ->eachById(function ($engagement) {
                $body = str($engagement->body)
                    ->replace(['\r\n', '\r', '\n'], '<br><br>')
                    ->toString();

                $body = json_decode($body);

                DB::table('engagements')
                    ->where('id', $engagement->id)
                    ->update([
                        'body' => tiptap_converter()
                            ->getEditor()
                            ->setContent($body)
                            ->getJSON(),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        //nop
    }
};
