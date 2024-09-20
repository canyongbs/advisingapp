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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'acad_career',
                'semester',
                'subject',
                'catalog_nbr',
                'enrl_status_reason',
                'enrl_add_dt',
                'enrl_drop_dt',
            ]);

            $table->string('division')->nullable()->change();
            $table->string('class_nbr')->nullable()->change();
            $table->string('crse_grade_off')->nullable()->change();
            $table->integer('unt_taken')->nullable()->change();
            $table->integer('unt_earned')->nullable()->change();
            $table->dateTimeTz('last_upd_dt_stmp')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('division')->nullable(false)->change();
            $table->string('class_nbr')->nullable(false)->change();
            $table->string('crse_grade_off')->nullable(false)->change();
            $table->integer('unt_taken')->nullable(false)->change();
            $table->integer('unt_earned')->nullable(false)->change();
            $table->dateTimeTz('last_upd_dt_stmp')->nullable(false)->change();

            $table->string('acad_career')->nullable();
            $table->string('semester')->nullable();
            $table->string('subject')->nullable();
            $table->string('catalog_nbr')->nullable();
            $table->string('enrl_status_reason')->nullable();
            $table->dateTimeTz('enrl_add_dt')->nullable();
            $table->dateTimeTz('enrl_drop_dt')->nullable();
        });
    }
};
