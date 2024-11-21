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

use App\Features\ResourceHub;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /** Rename knowledge_base_articles to resource_hub_articles*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_articles RENAME TO resource_hub_articles');

        DB::statement('CREATE VIEW knowledge_base_articles AS SELECT * FROM resource_hub_articles');

        DB::commit();

        /** Rename knowledge_base_categories to resource_hub_categories*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_categories RENAME TO resource_hub_categories');

        DB::statement('CREATE VIEW knowledge_base_categories AS SELECT * FROM resource_hub_categories');

        DB::commit();

        /** Rename knowledge_base_qualities to resource_hub_qualities*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_qualities RENAME TO resource_hub_qualities');

        DB::statement('CREATE VIEW knowledge_base_qualities AS SELECT * FROM resource_hub_qualities');

        DB::commit();

        if (ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS knowledge_base_qualities');
        }

        /** Rename knowledge_base_statuses to resource_hub_statuses*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_statuses RENAME TO resource_hub_statuses');

        DB::statement('CREATE VIEW knowledge_base_statuses AS SELECT * FROM resource_hub_statuses');

        DB::commit();
    }

    public function down(): void
    {
        /** Rename resource_hub_articles to knowledge_base_articles*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_articles');

        DB::statement('ALTER TABLE resource_hub_articles RENAME TO knowledge_base_articles');

        DB::commit();

        /** Rename resource_hub_categories to knowledge_base_categories*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_categories');

        DB::statement('ALTER TABLE resource_hub_categories RENAME TO knowledge_base_categories');

        DB::commit();

        /** Rename resource_hub_qualities to knowledge_base_qualities*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_qualities');

        DB::statement('ALTER TABLE resource_hub_qualities RENAME TO knowledge_base_qualities');

        DB::commit();

        /** Rename resource_hub_statuses to knowledge_base_statuses*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_statuses');

        DB::statement('ALTER TABLE resource_hub_statuses RENAME TO knowledge_base_statuses');

        DB::commit();
    }
};
