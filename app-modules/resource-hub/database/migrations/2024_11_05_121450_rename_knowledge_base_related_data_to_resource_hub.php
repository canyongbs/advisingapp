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
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        /**
         * Renaming the table division_knowledge_base_item to division_resource_hub_item
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME TO division_resource_hub_item');

        DB::statement('ALTER TABLE division_resource_hub_item RENAME CONSTRAINT division_knowledge_base_item_division_id_foreign TO division_resource_hub_item_division_id_foreign');

        DB::statement('ALTER TABLE division_resource_hub_item RENAME CONSTRAINT division_knowledge_base_item_knowledge_base_item_id_foreign TO division_resource_hub_item_resource_hub_item_id_foreign');

        DB::statement('ALTER TABLE division_resource_hub_item RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW division_knowledge_base_item AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM division_resource_hub_item');

        DB::commit();

        /**
         * Renaming the table knowledge_base_item_upvotes to resource_hub_item_upvotes
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME TO resource_hub_item_upvotes');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME CONSTRAINT knowledge_base_item_upvotes_pkey TO resource_hub_item_upvotes_pkey');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME CONSTRAINT knowledge_base_item_upvotes_knowledge_base_item_id_foreign TO resource_hub_item_upvotes_resource_hub_item_id_foreign');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME CONSTRAINT knowledge_base_item_upvotes_user_id_foreign TO resource_hub_item_upvotes_user_id_foreign');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW knowledge_base_item_upvotes AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_upvotes');

        DB::commit();

        /**
         * Renaming the table knowledge_base_item_views to resource_hub_item_views
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME TO resource_hub_item_views');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME CONSTRAINT knowledge_base_item_views_pkey TO resource_hub_item_views_pkey');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME CONSTRAINT knowledge_base_item_views_knowledge_base_item_id_foreign TO resource_hub_item_views_resource_hub_item_id_foreign');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME CONSTRAINT knowledge_base_item_views_user_id_foreign TO resource_hub_item_views_user_id_foreign');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW knowledge_base_item_views AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_views');

        DB::commit();
    }

    public function down(): void
    {
        /*** Renaming the table division_resource_hub_item to division_knowledge_base_item
         ** Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS division_knowledge_base_item');

        DB::statement('ALTER TABLE division_resource_hub_item RENAME TO division_knowledge_base_item');

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME CONSTRAINT division_resource_hub_item_division_id_foreign TO division_knowledge_base_item_division_id_foreign');

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME CONSTRAINT division_resource_hub_item_resource_hub_item_id_foreign TO division_knowledge_base_item_knowledge_base_item_id_foreign');

        DB::commit();

        /*** Renaming the table resource_hub_item_upvotes to knowledge_base_item_upvotes
         ** Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_upvotes');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME TO knowledge_base_item_upvotes');

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME CONSTRAINT resource_hub_item_upvotes_pkey TO knowledge_base_item_upvotes_pkey');

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME CONSTRAINT resource_hub_item_upvotes_resource_hub_item_id_foreign TO knowledge_base_item_upvotes_knowledge_base_item_id_foreign');

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME CONSTRAINT resource_hub_item_upvotes_user_id_foreign TO knowledge_base_item_upvotes_user_id_foreign');

        DB::commit();

        /*** Renaming the table resource_hub_item_views to knowledge_base_item_views
         ** Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_views');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME TO knowledge_base_item_views');

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME CONSTRAINT resource_hub_item_views_resource_hub_item_id_foreign TO knowledge_base_item_views_knowledge_base_item_id_foreign');

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME CONSTRAINT resource_hub_item_views_user_id_foreign TO knowledge_base_item_views_user_id_foreign');

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME CONSTRAINT resource_hub_item_views_pkey TO knowledge_base_item_views_pkey');

        DB::commit();
    }
};
