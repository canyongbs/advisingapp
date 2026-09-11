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

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptType;
use AdvisingApp\Ai\Models\Scopes\ConfidentialPromptScope;
use AdvisingApp\Ai\Settings\AiSettings;
use App\Http\Requests\Tenants\SyncTenantRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncTenantSmartPrompts
{
    public function execute(SyncTenantRequest $request): void
    {
        $smartPrompts = $request->validated('smartPrompts');

        if (! is_null($smartPrompts)) {
            assert(is_array($smartPrompts));

            $this->assertNoConflictingCustomPrompts($smartPrompts);
        }

        DB::transaction(function () use ($request, $smartPrompts) {
            $this->syncInstructions($request);

            if (is_null($smartPrompts)) {
                return;
            }

            $promptIds = [];

            foreach ($smartPrompts as $smartPromptCategory) {
                assert(is_array($smartPromptCategory));

                foreach ($smartPromptCategory['smart_prompts'] ?? [] as $smartPrompt) {
                    assert(is_array($smartPrompt));

                    $promptIds[] = $smartPrompt['id'];
                }
            }

            Prompt::query()
                ->where('is_smart', true)
                ->whereKeyNot($promptIds)
                ->delete();

            foreach ($smartPrompts as $smartPromptCategory) {
                $promptType = PromptType::query()
                    ->firstOrCreate(
                        ['title' => $smartPromptCategory['title']],
                        ['description' => $smartPromptCategory['description'] ?? null],
                    );

                foreach ($smartPromptCategory['smart_prompts'] ?? [] as $smartPrompt) {
                    /** @var Prompt $prompt */
                    $prompt = Prompt::find($smartPrompt['id']) ?? new Prompt();

                    $prompt->id = $smartPrompt['id'];
                    $prompt->title = $smartPrompt['title'];
                    $prompt->description = $smartPrompt['description'] ?? null;
                    $prompt->prompt = $smartPrompt['prompt'];
                    $prompt->type_id = $promptType->getKey();
                    $prompt->is_smart = true;
                    $prompt->save();
                }
            }
        });
    }

    private function syncInstructions(SyncTenantRequest $request): void
    {
        $instructions = $request->validated('smartPromptInstructions');

        if (blank($instructions)) {
            return;
        }

        $settings = app(AiSettings::class);
        $settings->smart_prompt_instructions = $instructions;
        $settings->save();
    }

    /**
     * Smart prompt titles are checked here, rather than in `SyncTenantRequest`, because
     * this runs once the tenant is current. The request is validated on the landlord-api
     * middleware group, before the tenant connection is configured.
     *
     * @param array<int, mixed> $smartPrompts
     */
    private function assertNoConflictingCustomPrompts(array $smartPrompts): void
    {
        $errors = [];

        foreach ($smartPrompts as $categoryIndex => $smartPromptCategory) {
            assert(is_array($smartPromptCategory));

            $promptType = PromptType::query()->where('title', (string) ($smartPromptCategory['title'] ?? ''))->first();

            if (! $promptType) {
                continue;
            }

            foreach ($smartPromptCategory['smart_prompts'] ?? [] as $promptIndex => $smartPrompt) {
                assert(is_array($smartPrompt));

                $title = (string) ($smartPrompt['title'] ?? '');

                $conflictsWithExistingCustomPrompt = Prompt::withoutGlobalScope(ConfidentialPromptScope::class)
                    ->where('type_id', $promptType->getKey())
                    ->where('is_smart', false)
                    ->where('title', $title)
                    ->exists();

                if ($conflictsWithExistingCustomPrompt) {
                    $errors["smartPrompts.{$categoryIndex}.smart_prompts.{$promptIndex}.title"] = 'The smart prompt title conflicts with an existing custom prompt in this category.';
                }
            }
        }

        if (filled($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
