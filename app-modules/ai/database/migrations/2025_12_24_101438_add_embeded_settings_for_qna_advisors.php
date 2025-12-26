<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->string('title_text_color')->nullable();
            $table->string('description_text_color')->nullable();
            $table->string('button_text_color')->nullable();
            $table->string('button_text_hover_color')->nullable();
            $table->string('button_background_color')->nullable();
            $table->string('button_background_hover_color')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->dropColumn([
                'title_text_color',
                'description_text_color',
                'button_text_color',
                'button_text_hover_color',
                'button_background_color',
                'button_background_hover_color',
            ]);
        });
    }
};
