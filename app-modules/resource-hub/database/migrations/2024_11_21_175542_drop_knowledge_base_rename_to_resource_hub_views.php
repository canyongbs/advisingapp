<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS knowledge_base_articles');

        DB::statement('DROP VIEW IF EXISTS knowledge_base_categories');

        DB::statement('DROP VIEW IF EXISTS knowledge_base_statuses');

        DB::statement('DROP VIEW IF EXISTS division_knowledge_base_item');

        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_upvotes');

        DB::statement('DROP VIEW IF EXISTS knowledge_base_item_views');
    }

    public function down(): void
    {
        DB::statement('CREATE VIEW knowledge_base_articles AS SELECT * FROM resource_hub_articles');

        DB::statement('CREATE VIEW knowledge_base_categories AS SELECT * FROM resource_hub_categories');

        DB::statement('CREATE VIEW knowledge_base_statuses AS SELECT * FROM resource_hub_statuses');

        DB::statement('CREATE VIEW division_knowledge_base_item AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM division_resource_hub_item');

        DB::statement('CREATE VIEW knowledge_base_item_upvotes AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_upvotes');

        DB::statement('CREATE VIEW knowledge_base_item_views AS SELECT resource_hub_item_id AS knowledge_base_item_id FROM resource_hub_item_views');
    }
};
