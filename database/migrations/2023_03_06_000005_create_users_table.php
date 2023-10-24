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
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('locale')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_external')->default(false);
            $table->text('bio')->nullable();
            $table->boolean('is_bio_visible_on_profile')->default(false);
            $table->string('avatar_url')->nullable();
            $table->boolean('are_teams_visible_on_profile')->default(false);
            $table->string('timezone')->default('UTC');

            $table->foreignUuid('pronouns_id')->nullable()->constrained('pronouns')->nullOnDelete();
            $table->boolean('are_pronouns_visible_on_profile')->default(false);

            $table->datetime('email_verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
