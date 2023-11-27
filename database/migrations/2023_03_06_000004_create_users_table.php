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
            $table->boolean('is_division_visible_on_profile')->default(false);
            $table->string('timezone')->default('UTC');
            $table->boolean('office_hours_are_enabled')->default(false);
            $table->boolean('appointments_are_restricted_to_existing_students')->default(false);
            $table->json('office_hours_days')->nullable();
            $table->boolean('out_of_office_is_enabled')->default(false);
            $table->datetime('out_of_office_starts_at')->nullable();
            $table->datetime('out_of_office_ends_at')->nullable();

            $table->foreignUuid('pronouns_id')->nullable()->constrained('pronouns')->nullOnDelete();
            $table->boolean('are_pronouns_visible_on_profile')->default(false);
            $table->boolean('default_assistant_chat_folders_created')->default(false);

            $table->datetime('email_verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
