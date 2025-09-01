<?php

use App\Features\OpenAiResponsesApiSettingsFeature;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifySettings;
use Illuminate\Contracts\Encryption\DecryptException;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    use CanModifySettings;

    public function up(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('ai.is_open_ai_gpt_4o_responses_api_enabled');
            $this->migrator->deleteIfExists('ai.is_open_ai_gpt_4o_mini_responses_api_enabled');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_model_name');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_base_uri');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_api_key');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_model');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_applicable_features');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_model_name');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_base_uri');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_api_key');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_model');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_applicable_features');
            $this->migrator->deleteIfExists('ai.is_open_ai_gpt_41_mini_responses_api_enabled');
            $this->migrator->deleteIfExists('ai.is_open_ai_gpt_41_nano_responses_api_enabled');
            $this->migrator->deleteIfExists('ai.is_open_ai_gpt_o4_mini_responses_api_enabled');

            foreach ([
                'open_ai_gpt_4o_base_uri',
                'open_ai_gpt_4o_mini_base_uri',
                'open_ai_gpt_o3_base_uri',
                'open_ai_gpt_41_mini_base_uri',
                'open_ai_gpt_41_nano_base_uri',
                'open_ai_gpt_o4_mini_base_uri',
                'open_ai_gpt_5_base_uri',
                'open_ai_gpt_5_mini_base_uri',
                'open_ai_gpt_5_nano_base_uri',
            ] as $baseUriProperty) {
                try {
                    $this->updateSettings('ai', $baseUriProperty, function (mixed $payload): string {
                        if (blank($payload)) {
                            return '';
                        }

                        if (! is_string($payload)) {
                            return '';
                        }

                        $newPayload = rtrim(trim($payload), '/v1');

                        DB::table('open_ai_vector_stores')
                            ->where('deployment_hash', md5($payload))
                            ->update(['deployment_hash' => md5($newPayload)]);

                        DB::table('open_ai_research_request_vector_stores')
                            ->where('deployment_hash', md5($payload))
                            ->update(['deployment_hash' => md5($newPayload)]);

                        return $newPayload;
                    }, isEncrypted: true);
                } catch (DecryptException) {
                    // Blank URIs that fail decryption do not need to be processed.
                }
            }

            OpenAiResponsesApiSettingsFeature::activate();
        });
    }

    public function down(): void
    {
        // This cannot be fully undone as it involves data loss (removal of settings properties).
    }
};
