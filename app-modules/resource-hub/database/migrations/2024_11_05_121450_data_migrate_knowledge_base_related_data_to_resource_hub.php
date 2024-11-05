<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /**
         * Renaming the table division_knowledge_base_item to division_resource_hub_item
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME TO division_resource_hub_item');

        DB::statement('ALTER TABLE division_resource_hub_item RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW division_knowledge_base_item AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM division_resource_hub_item');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS division_knowledge_base_item');

        /**
         * Renaming the table knowledge_base_item_upvotes to resource_hub_item_upvotes
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME TO resource_hub_item_upvotes');

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW knowledge_base_item_upvotes AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_upvotes');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_upvotes');

        /**
         * Renaming the table knowledge_base_item_views to resource_hub_item_views
         * Renaming the column knowledge_base_item_id to resource_hub_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME TO resource_hub_item_views');

        DB::statement('ALTER TABLE resource_hub_item_views RENAME COLUMN knowledge_base_item_id TO resource_hub_item_id');

        DB::statement('CREATE VIEW knowledge_base_item_views AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_views');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_views');
    }

    public function down(): void
    {
        /**
         * Renaming the table division_resource_hub_item to division_knowledge_base_item
         * Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE division_resource_hub_item RENAME TO division_knowledge_base_item');

        DB::statement('ALTER TABLE division_knowledge_base_item RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('CREATE VIEW division_resource_hub_item AS SELECT knowledge_base_item_id AS resource_hub_item_id FROM division_knowledge_base_item');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS division_resource_hub_item');

        /**
         * Renaming the table resource_hub_item_upvotes to knowledge_base_item_upvotes
         * Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_item_upvotes RENAME TO knowledge_base_item_upvotes');

        DB::statement('ALTER TABLE knowledge_base_item_upvotes RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('CREATE VIEW resource_hub_item_upvotes AS SELECT knowledge_base_item_id AS resource_hub_item_id FROM knowledge_base_item_upvotes');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS resource_hub_item_upvotes');

        /**
        * Renaming the table resource_hub_item_views to knowledge_base_item_views
        * Renaming the column resource_hub_item_id to knowledge_base_item_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_item_views RENAME TO knowledge_base_item_views');

        DB::statement('ALTER TABLE knowledge_base_item_views RENAME COLUMN resource_hub_item_id TO knowledge_base_item_id');

        DB::statement('CREATE VIEW resource_hub_item_views AS SELECT knowledge_base_item_id AS resource_hub_item_id FROM knowledge_base_item_views');

        DB::commit();

        /** Drop view */
        DB::statement('DROP VIEW IF EXISTS resource_hub_item_views');
    }
};
