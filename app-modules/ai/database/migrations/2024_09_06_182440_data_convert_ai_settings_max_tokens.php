<?php

use App\Enums\FeatureFlag;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::beginTransaction();

        $maxTokensSetting = DB::table('settings')
            ->where('group', 'ai')
            ->where('name', 'max_tokens')
            ->first();

        if ($maxTokensSetting) {
            $maxTokens = $maxTokensSetting->payload;

            $newPayload = match ($maxTokens) {
                150 => 500,
                350 => 1000,
                500 => 2500,
                default => 500,
            };

            DB::table('settings')
                ->where('group', 'ai')
                ->where('name', 'max_tokens')
                ->update(['payload' => $newPayload]);
        }

        FeatureFlag::AiSettingsMaxTokensUpdate->activate();

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        $maxTokensSetting = DB::table('settings')
            ->where('group', 'ai')
            ->where('name', 'max_tokens')
            ->first();

        if ($maxTokensSetting) {
            $maxTokens = $maxTokensSetting->payload;

            $newPayload = match ($maxTokens) {
                500 => 150,
                1000 => 350,
                2500 => 500,
                default => 150,
            };

            DB::table('settings')
                ->where('group', 'ai')
                ->where('name', 'max_tokens')
                ->update(['payload' => $newPayload]);
        }

        FeatureFlag::AiSettingsMaxTokensUpdate->deactivate();

        DB::commit();
    }
};
