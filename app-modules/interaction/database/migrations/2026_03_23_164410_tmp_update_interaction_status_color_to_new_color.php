<?php

use App\Features\InteractionStatusColorFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('interaction_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $newColor = match ($status->color) {
                        'success' => 'green',
                        'danger' => 'red',
                        'warning' => 'yellow',
                        'info' => 'sky',
                        'primary' => 'blue',
                        'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($newColor !== $status->color) {
                        DB::table('interaction_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $newColor]);
                    }
                }
            });
            InteractionStatusColorFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            InteractionStatusColorFeature::deactivate();

            DB::table('interaction_statuses')->chunkById(100, function (Collection $statuses) {
                foreach ($statuses as $status) {
                    $oldColor = match ($status->color) {
                        'green' => 'success',
                        'red' => 'danger',
                        'yellow' => 'warning',
                        'sky' => 'info',
                        'blue' => 'primary',
                        'gray' => 'gray',
                        default => 'gray',
                    };

                    if ($oldColor !== $status->color) {
                        DB::table('interaction_statuses')
                            ->where('id', $status->id)
                            ->update(['color' => $oldColor]);
                    }
                }
            });
        });
    }
};
