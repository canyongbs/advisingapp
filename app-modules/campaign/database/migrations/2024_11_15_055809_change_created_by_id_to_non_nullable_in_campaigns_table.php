<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->uuid('created_by_id')->nullable(false)->change();
            $table->string('created_by_type')->nullable(false)->change();
        });
    }
};
