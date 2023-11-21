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

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject')->nullable();
            $table->longText('description')->nullable();

            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('interactable_id')->nullable();
            $table->string('interactable_type')->nullable();

            $table->foreignUuid('interaction_type_id')->nullable()->constrained('interaction_types');
            $table->foreignUuid('interaction_relation_id')->nullable()->constrained('interaction_relations');
            $table->foreignUuid('interaction_campaign_id')->nullable()->constrained('interaction_campaigns');
            $table->foreignUuid('interaction_driver_id')->nullable()->constrained('interaction_drivers');
            $table->foreignUuid('interaction_status_id')->nullable()->constrained('interaction_statuses');
            $table->foreignUuid('interaction_outcome_id')->nullable()->constrained('interaction_outcomes');
            $table->foreignUuid('division_id')->nullable()->constrained('divisions');

            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();

            $table->timestamps();
        });
    }
};
