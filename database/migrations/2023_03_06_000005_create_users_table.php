<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('emplid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->datetime('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('locale')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_external')->default(false);

            $table->string('calendar_type')->nullable();
            $table->text('calendar_id')->nullable();
            $table->text('calendar_token')->nullable();
            $table->text('calendar_refresh_token')->nullable();
            $table->timestamp('calendar_token_expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
