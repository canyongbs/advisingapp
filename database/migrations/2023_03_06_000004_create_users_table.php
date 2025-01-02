<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('emplid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('is_email_visible_on_profile')->default(false);
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
            $table->boolean('has_enabled_public_profile')->default(false);
            $table->string('public_profile_slug')->nullable()->unique();
            $table->boolean('office_hours_are_enabled')->default(false);
            $table->boolean('appointments_are_restricted_to_existing_students')->default(false);
            $table->jsonb('office_hours')->nullable();
            $table->boolean('out_of_office_is_enabled')->default(false);
            $table->datetime('out_of_office_starts_at')->nullable();
            $table->datetime('out_of_office_ends_at')->nullable();
            $table->text('phone_number')->nullable();
            $table->boolean('is_phone_number_visible_on_profile')->default(false);
            $table->boolean('working_hours_are_enabled')->default(false);
            $table->boolean('are_working_hours_visible_on_profile')->default(false);
            $table->jsonb('working_hours')->nullable();
            $table->string('job_title')->nullable();

            $table->foreignUuid('pronouns_id')->nullable()->constrained('pronouns')->nullOnDelete();
            $table->boolean('are_pronouns_visible_on_profile')->default(false);
            $table->boolean('default_assistant_chat_folders_created')->default(false);

            $table->datetime('email_verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
