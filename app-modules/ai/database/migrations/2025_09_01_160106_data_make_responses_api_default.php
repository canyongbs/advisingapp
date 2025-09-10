<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
        });
    }

    public function down(): void
    {
        // This cannot be fully undone as it involves data loss (removal of settings properties).
    }
};
