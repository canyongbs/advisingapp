<?php

use App\Features\ConfidentialPromptsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('prompts', function (Blueprint $table) {
                $table->boolean('is_confidential')
                    ->default(false);
            });

            ConfidentialPromptsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ConfidentialPromptsFeature::deactivate();

            Schema::table('prompts', function (Blueprint $table) {
                $table->dropColumn('is_confidential');
            });
        });
    }
};
