<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');

            $table->string('preferred')->nullable();
            $table->longText('description')->nullable();

            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->integer('mobile')->nullable();
            // TODO Does this need to be a boolean with a default of false?
            $table->string('sms_opt_out')->nullable();
            $table->string('email_bounce')->nullable();
            $table->integer('phone')->nullable();

            $table->string('address')->nullable();
            $table->string('address_2')->nullable();

            $table->date('date_of_birth')->nullable();
            // TODO Is this supposed to be a boolean? Or enum status of sorts?
            $table->string('highschool_grad')->nullable();
            // TODO Does this just need to be a year?
            $table->date('highschool_date')->nullable();

            $table->foreign('status_id')->references('id')->on('prospect_statuses');
            $table->foreign('source_id')->references('id')->on('prospect_sources');
            // TODO Determine if there can be more than one assignment to a prospect
            $table->foreign('assigned_to_id')->references('id')->on('users');
            // TODO Is this potentially nullable? Might we eventually have prospects created by the system in some capacity?
            $table->foreign('created_by_id')->references('id')->on('users');
            // End refactor

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
