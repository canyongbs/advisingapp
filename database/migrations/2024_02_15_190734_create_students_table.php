<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            return;
        }

        Schema::create('students', function (Blueprint $table) {
            $table->string('sisid')->primary();
            $table->string('otherid')->nullable();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('full_name')->nullable();
            $table->string('preferred')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('sms_opt_out')->default(false);
            $table->boolean('email_bounce')->default(false);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('hsgrad')->nullable();
            $table->boolean('dual')->default(false);
            $table->boolean('ferpa')->default(false);
            $table->date('dfw')->nullable();
            $table->boolean('sap')->default(false);
            $table->string('holds')->nullable();
            $table->boolean('firstgen')->default(false);
            $table->string('ethnicity')->nullable();
            $table->timestamp('lastlmslogin')->nullable();
            $table->string('f_e_term')->nullable();
            $table->string('mr_e_term')->nullable();
        });
    }
};
