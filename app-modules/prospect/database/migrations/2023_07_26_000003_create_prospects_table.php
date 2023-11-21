<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectsTable extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->uuid('id')->primary();
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
