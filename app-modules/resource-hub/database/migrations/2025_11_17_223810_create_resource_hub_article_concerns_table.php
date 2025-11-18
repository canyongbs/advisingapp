<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('resource_hub_article_concerns', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('description');
            $table->foreignUuid('created_by_id')->constrained('users');
            $table->string('status');
            $table->foreignUuid('resource_hub_article_id')->constrained('resource_hub_articles');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_hub_article_concerns');
    }
};
