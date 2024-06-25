
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignUuid('segment_id')->constrained('segments');
            $table->dropForeign(['caseload_id']);
            $table->foreignUuid('caseload_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['segment_id']);
            $table->dropColumn('segment_id');
            $table->foreignUuid('caseload_id')->constrained('caseloads')->change();
        });
    }
};
