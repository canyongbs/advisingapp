<?php

use Database\Migrations\Concerns\CanModifySettings;
use Exception;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifySettings;

    public function up(): void
    {
        DB::transaction(function () {
            $openAiGpt35ApplicableFeatures = [];
            $openAiGpt4ApplicableFeatures = [];
            $openAiGpt4oApplicableFeatures = [];
            $openAiGpt4oMiniApplicableFeatures = [];
            $openAiGptO1MiniApplicableFeatures = [];
            $openAiGptO3MiniApplicableFeatures = [];
            $openAiGpt41MiniApplicableFeatures = [];
            $openAiGpt41NanoApplicableFeatures = [];

            $logApplicableFeature = function (string $model, string $feature) use (&$openAiGpt35ApplicableFeatures, &$openAiGpt4ApplicableFeatures, &$openAiGpt4oApplicableFeatures, &$openAiGpt4oMiniApplicableFeatures, &$openAiGptO1MiniApplicableFeatures, &$openAiGptO3MiniApplicableFeatures, &$openAiGpt41MiniApplicableFeatures, &$openAiGpt41NanoApplicableFeatures) {
                match ($model) {
                    'openai_gpt_3.5' => $openAiGpt35ApplicableFeatures[$feature] = true,
                    'openai_gpt_4' => $openAiGpt4ApplicableFeatures[$feature] = true,
                    'openai_gpt_4o' => $openAiGpt4oApplicableFeatures[$feature] = true,
                    'openai_gpt_4o_mini' => $openAiGpt4oMiniApplicableFeatures[$feature] = true,
                    'openai_gpt_o1_mini' => $openAiGptO1MiniApplicableFeatures[$feature] = true,
                    'openai_gpt_o3_mini' => $openAiGptO3MiniApplicableFeatures[$feature] = true,
                    'openai_gpt_41_mini' => $openAiGpt41MiniApplicableFeatures[$feature] = true,
                    'openai_gpt_41_nano' => $openAiGpt41NanoApplicableFeatures[$feature] = true,
                    default => throw new Exception("Unexpected model encountered: [{$model}]."),
                };
            };

            $logApplicableFeature(
                model: json_decode(
                    DB::table('settings')
                        ->where('group', 'ai-integrated-assistant')
                        ->where('name', 'default_model')
                        ->value('payload'),
                ) ?? 'openai_gpt_4o',
                feature: 'integrated_advisor',
            );

            $logApplicableFeature(
                model: json_decode(
                    DB::table('settings')
                        ->where('group', 'ai')
                        ->where('name', 'default_model')
                        ->value('payload'),
                ) ?? 'openai_gpt_4o',
                feature: 'institutional_advisor',
            );

            DB::table('ai_assistants')
                ->eachById(fn (object $assistant) => $logApplicableFeature(
                    $assistant->model,
                    feature: $assistant->is_default ? 'custom_advisors' : 'institutional_advisor',
                ));

            $this->updateSettings('ai', 'open_ai_gpt_35_applicable_features', fn (): array => array_keys($openAiGpt35ApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_4_applicable_features', fn (): array => array_keys($openAiGpt4ApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_4o_applicable_features', fn (): array => array_keys($openAiGpt4oApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_4o_mini_applicable_features', fn (): array => array_keys($openAiGpt4oMiniApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_o1_mini_applicable_features', fn (): array => array_keys($openAiGptO1MiniApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_o3_mini_applicable_features', fn (): array => array_keys($openAiGptO3MiniApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_41_mini_applicable_features', fn (): array => array_keys($openAiGpt41MiniApplicableFeatures));
            $this->updateSettings('ai', 'open_ai_gpt_41_nano_applicable_features', fn (): array => array_keys($openAiGpt41NanoApplicableFeatures));
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->updateSettings('ai', 'open_ai_gpt_35_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_4_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_4o_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_4o_mini_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_o1_mini_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_o3_mini_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_41_mini_applicable_features', fn (): array => []);
            $this->updateSettings('ai', 'open_ai_gpt_41_nano_applicable_features', fn (): array => []);
        });
    }
};
