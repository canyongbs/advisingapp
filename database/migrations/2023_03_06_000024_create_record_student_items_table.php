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

class CreateRecordStudentItemsTable extends Migration
{
    public function up()
    {
        Schema::create('record_student_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sisid');
            $table->string('otherid')->nullable();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('full')->nullable();
            $table->string('preferred')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->integer('mobile')->nullable();
            $table->string('sms_opt_out')->nullable();
            $table->string('email_bounce')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('hsgrad')->nullable();
            $table->string('dual')->nullable();
            $table->string('ferpa')->nullable();
            $table->float('gpa', 4, 3)->nullable();
            $table->string('dfw')->nullable();
            $table->string('firstgen')->nullable();
            $table->string('ethnicity')->nullable();
            $table->datetime('lastlmslogin')->nullable();
            $table->timestamps();
        });
    }
}
