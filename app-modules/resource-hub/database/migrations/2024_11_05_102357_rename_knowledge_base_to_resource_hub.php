<?php

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

        if (ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS knowledge_base_articles');
        }

        /** Rename knowledge_base_categories to resource_hub_categories*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_categories RENAME TO resource_hub_categories');

        DB::statement('CREATE VIEW knowledge_base_categories AS SELECT * FROM resource_hub_categories');

        DB::commit();

        if (ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS knowledge_base_categories');
        }

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

        if (ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS knowledge_base_statuses');
        }
    }

    public function down(): void
    {
        /** Rename resource_hub_articles to knowledge_base_articles*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_articles RENAME TO knowledge_base_articles');

        DB::statement('CREATE VIEW resource_hub_articles AS SELECT * FROM knowledge_base_articles');

        DB::commit();

        if (! ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS resource_hub_articles');
        }

        /** Rename resource_hub_categories to knowledge_base_categories*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_categories RENAME TO knowledge_base_categories');

        DB::statement('CREATE VIEW resource_hub_categories AS SELECT * FROM knowledge_base_categories');

        DB::commit();

        if (! ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS resource_hub_categories');
        }

        /** Rename resource_hub_qualities to knowledge_base_qualities*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_qualities RENAME TO knowledge_base_qualities');

        DB::statement('CREATE VIEW resource_hub_qualities AS SELECT * FROM knowledge_base_qualities');

        DB::commit();

        if (! ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS resource_hub_qualities');
        }

        /** Rename resource_hub_statuses to knowledge_base_statuses*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE resource_hub_statuses RENAME TO knowledge_base_statuses');

        DB::statement('CREATE VIEW resource_hub_statuses AS SELECT * FROM knowledge_base_statuses');

        DB::commit();

        if (! ResourceHub::active()) {
            DB::statement('DROP VIEW IF EXISTS resource_hub_statuses');
        }
    }
};
