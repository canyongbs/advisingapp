<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectsTable extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            //Changed from uuid to resolve postgres inner join casting issue
            $table->string('id')->primary();
            $table->foreignUuid('status_id')->references('id')->on('prospect_statuses');
            $table->foreignUuid('source_id')->references('id')->on('prospect_sources');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('preferred')->nullable();
            $table->longText('description')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('sms_opt_out')->default(false);
            $table->boolean('email_bounce')->default(false);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('hsgrad')->nullable();
            // TODO Determine if there can be more than one assignment to a prospect
            $table->foreignUuid('assigned_to_id')->nullable()->references('id')->on('users');
            $table->foreignUuid('created_by_id')->nullable()->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
