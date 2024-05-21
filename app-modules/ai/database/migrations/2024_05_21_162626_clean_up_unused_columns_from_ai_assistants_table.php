<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('application')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('application')->nullable()->change();
            $table->string('model')->nullable()->change();
        });

        DB::table('ai_assistants')
            ->update([
                'type' => new Expression('CASE WHEN ai_assistants.is_default = true THEN \'default\' ELSE \'custom\' END'),
            ]);
    }
};
