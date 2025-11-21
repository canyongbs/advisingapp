<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->index(
                ['recipient_type', 'recipient_id', 'deleted_at'],
                'engagements_recipient_type_id_deleted_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->dropIndex('engagements_recipient_type_id_deleted_at_index');
        });
    }
};
