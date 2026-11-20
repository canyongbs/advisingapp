<?php

use App\Features\PasswordFormFieldFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * @var array<int, string>
     */
    private array $submissionTables = [
        'form_submissions',
        'application_submissions',
        'event_registration_form_submissions',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            Schema::create('secrets', function (Blueprint $table): void {
                $table->uuid('id')->primary();

                $table->text('value');
                $table->nullableUuidMorphs('related');

                $table->timestamps();

                $table->index(['related_id', 'updated_at']);
            });

            foreach ($this->submissionTables as $submissionTable) {
                Schema::table($submissionTable, function (Blueprint $table): void {
                    $table->text('secret_key')->nullable();
                });
            }

            PasswordFormFieldFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            PasswordFormFieldFeature::deactivate();

            foreach ($this->submissionTables as $submissionTable) {
                Schema::table($submissionTable, function (Blueprint $table): void {
                    $table->dropColumn('secret_key');
                });
            }

            Schema::dropIfExists('secrets');
        });
    }
};
