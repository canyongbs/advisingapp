<?php

use AdvisingApp\Alert\Configurations\LowEarnedCreditPercentageAlertConfiguration;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $configuration = LowEarnedCreditPercentageAlertConfiguration::create([
                'minimum_earned_credit_percentage' => null,
            ]);

            $configuration->alertConfiguration()->create([
                'preset' => AlertPreset::LowEarnedCreditPercentage->value,
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AlertConfiguration::where('preset', AlertPreset::LowEarnedCreditPercentage->value)
                ->each(function (AlertConfiguration $config) {
                    $config->configuration?->delete();
                    $config->delete();
                });
        });
    }
};
