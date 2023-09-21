<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('caseloads', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->json('filters')->nullable();
            $table->string('model');
            $table->string('type');

            $table->foreignIdFor(User::class)->constrained();

            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('caseload_static_subjects', function (Blueprint $table) {
        //     $table->string('subject_id');
        //     $table->string('subject_type');
        //     $table->morphs('caseloadable');
        //     $table->timestamps();
        // });
    }
};
