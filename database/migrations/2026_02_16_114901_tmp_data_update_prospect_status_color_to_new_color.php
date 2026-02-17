<?php

use App\Features\ProspectStatusFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE prospect_statuses DISABLE TRIGGER prevent_modification_of_system_protected_rows');

            DB::table('prospect_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $newColor = match ($status->color) {
                        'success' => 'green',
                        'danger' => 'red',
                        'warning' => 'amber',
                        'info' => 'blue',
                        'primary', 'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($newColor !== $status->color) {
                        DB::table('prospect_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $newColor]);
                    }
                }
            });

            DB::statement('ALTER TABLE prospect_statuses ENABLE TRIGGER prevent_modification_of_system_protected_rows');

            ProspectStatusFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ProspectStatusFeature::deactivate();

            DB::statement('ALTER TABLE prospect_statuses DISABLE TRIGGER prevent_modification_of_system_protected_rows');

            DB::table('prospect_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $oldColor = match ($status->color) {
                        'green' => 'success',
                        'red' => 'danger',
                        'amber' => 'warning',
                        'blue' => 'info',
                        'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($oldColor !== $status->color) {
                        DB::table('prospect_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $oldColor]);
                    }
                }
            });

            DB::statement('ALTER TABLE prospect_statuses ENABLE TRIGGER prevent_modification_of_system_protected_rows');
        });
    }
};
