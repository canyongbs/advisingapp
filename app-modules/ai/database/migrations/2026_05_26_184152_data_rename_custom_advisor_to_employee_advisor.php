<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Exceptions\SettingDoesNotExist;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    /**
     * @var list<string>
     */
    protected array $applicableFeatureKeys = [
        'ai.open_ai_gpt_4o_applicable_features',
        'ai.open_ai_gpt_4o_mini_applicable_features',
        'ai.open_ai_gpt_o3_applicable_features',
        'ai.open_ai_gpt_41_mini_applicable_features',
        'ai.open_ai_gpt_41_nano_applicable_features',
        'ai.open_ai_gpt_o4_mini_applicable_features',
        'ai.open_ai_gpt_5_applicable_features',
        'ai.open_ai_gpt_5_mini_applicable_features',
        'ai.open_ai_gpt_54_mini_applicable_features',
        'ai.open_ai_gpt_5_nano_applicable_features',
        'ai.open_ai_gpt_54_nano_applicable_features',
        'ai.jina_deepsearch_v1_applicable_features',
    ];

    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction
    public function up(): void
    {
        DB::transaction(function () {
            $this->renameIfExists(
                'ai-custom-advisor.allow_selection_of_model',
                'ai-employee-advisor.allow_selection_of_model',
            );

            $this->renameIfExists(
                'ai-custom-advisor.preselected_model',
                'ai-employee-advisor.preselected_model',
            );

            foreach ($this->applicableFeatureKeys as $key) {
                $this->mapApplicableFeatures($key, 'custom_advisors', 'employee_advisors');
            }
        });
    }

    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction
    public function down(): void
    {
        DB::transaction(function () {
            foreach ($this->applicableFeatureKeys as $key) {
                $this->mapApplicableFeatures($key, 'employee_advisors', 'custom_advisors');
            }

            $this->renameIfExists(
                'ai-employee-advisor.preselected_model',
                'ai-custom-advisor.preselected_model',
            );

            $this->renameIfExists(
                'ai-employee-advisor.allow_selection_of_model',
                'ai-custom-advisor.allow_selection_of_model',
            );
        });
    }

    protected function renameIfExists(string $from, string $to): void
    {
        try {
            $this->migrator->rename($from, $to);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
    }

    protected function mapApplicableFeatures(string $key, string $oldValue, string $newValue): void
    {
        try {
            $this->migrator->update($key, function (array $values) use ($oldValue, $newValue): array {
                return array_values(array_map(
                    fn (string $value): string => $value === $oldValue ? $newValue : $value,
                    $values,
                ));
            });
        } catch (SettingDoesNotExist $exception) {
            // do nothing
        }
    }
};
