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

use AdvisingApp\Ai\Actions\SyncTenantSmartPrompts;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptType;
use AdvisingApp\Ai\Settings\AiSettings;
use App\Features\PromptTitleUniquePerTypeFeature;
use App\Http\Requests\Tenants\SyncTenantRequest;
use Illuminate\Validation\ValidationException;

/**
 * @param array<string, mixed> $overrides
 */
function syncTenantRequest(array $overrides = []): SyncTenantRequest
{
    $payload = array_merge([
        'limits' => [
            'conversationalAiSeats' => 0,
            'retentionCrmSeats' => 0,
            'recruitmentCrmSeats' => 0,
            'emails' => 0,
            'sms' => 0,
            'dataAdvisorsCount' => 0,
            'resetDate' => '01-01',
            'employeeAdvisorsCount' => 0,
            'customerAdvisorsCount' => 0,
        ],
        'addons' => [
            'employeeAdvisors' => false,
            'customerAdvisors' => false,
            'onlineForms' => false,
            'onlineSurveys' => false,
            'onlineAdmissions' => false,
            'resourceHub' => false,
            'supportPrograms' => false,
            'eventManagement' => false,
            'realtimeChat' => false,
            'mobileApps' => false,
            'scheduleAndAppointments' => false,
            'researchAdvisor' => false,
            'dataAdvisor' => false,
            'earlyAlert' => false,
            'publicProfiles' => false,
        ],
        'subscription' => [
            'clientName' => 'Test Client',
            'partnerName' => 'Test Partner',
            'startDate' => now()->subYear()->toIso8601String(),
            'endDate' => now()->addYear()->toIso8601String(),
        ],
        'subscriptionStatus' => 'active',
    ], $overrides);

    $request = SyncTenantRequest::create('/', 'POST', $payload);
    $request->headers->set('Accept', 'application/json');
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->validateResolved();

    return $request;
}

it('syncs the smart prompt instructions into the ai settings', function () {
    $instructions = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Synced global smart prompt instructions.'],
                ],
            ],
        ],
    ];

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPromptInstructions' => $instructions,
    ]));

    expect(app(AiSettings::class)->smart_prompt_instructions)
        ->toBe($instructions);
});

it('does not overwrite the ai settings when no instructions are provided', function () {
    $existing = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Existing instructions.'],
                ],
            ],
        ],
    ];

    $settings = app(AiSettings::class);
    $settings->smart_prompt_instructions = $existing;
    $settings->save();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest());

    expect(app(AiSettings::class)->smart_prompt_instructions)
        ->toBe($existing);
});

it('creates the prompt types and smart prompts from the payload', function () {
    $promptId = fake()->uuid();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'description' => 'Recruitment prompts',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Draft an email',
                        'description' => 'Drafts an email to a prospect',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
        ],
    ]));

    $promptType = PromptType::query()->where('title', 'Recruitment')->sole();

    expect($promptType->description)->toBe('Recruitment prompts');

    $prompt = Prompt::find($promptId);

    expect($prompt)->not->toBeNull()
        ->and($prompt->title)->toBe('Draft an email')
        ->and($prompt->description)->toBe('Drafts an email to a prospect')
        ->and($prompt->prompt)->toBe('Write an email to a prospect.')
        ->and($prompt->type_id)->toBe($promptType->getKey())
        ->and($prompt->is_smart)->toBeTrue();
});

it('does not persist smart prompt changes until the deferred sync runs', function () {
    $promptId = fake()->uuid();

    $sync = app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Draft an email',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
        ],
    ]), defer: true);
    assert($sync instanceof Closure);

    expect(Prompt::find($promptId))->toBeNull();

    $sync();

    expect(Prompt::find($promptId))->not->toBeNull();
});

it('reuses an existing prompt type with the same title', function () {
    $promptType = PromptType::factory()->create(['title' => 'Retention']);

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Retention',
                'smart_prompts' => [
                    [
                        'id' => fake()->uuid(),
                        'title' => 'Re-engage a student',
                        'prompt' => 'Write a message to re-engage a student.',
                    ],
                ],
            ],
        ],
    ]));

    expect(PromptType::query()->where('title', 'Retention')->count())->toBe(1)
        ->and(Prompt::query()->where('is_smart', true)->sole()->type_id)->toBe($promptType->getKey());
});

it('updates an existing smart prompt with the same id', function () {
    $promptId = fake()->uuid();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Original title',
                        'prompt' => 'Original prompt.',
                    ],
                ],
            ],
        ],
    ]));

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Updated title',
                        'prompt' => 'Updated prompt.',
                    ],
                ],
            ],
        ],
    ]));

    expect(Prompt::query()->where('is_smart', true)->count())->toBe(1);

    $prompt = Prompt::find($promptId);

    expect($prompt->title)->toBe('Updated title')
        ->and($prompt->prompt)->toBe('Updated prompt.');
});

it('deletes smart prompts that are no longer in the payload', function () {
    $staleId = fake()->uuid();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $staleId,
                        'title' => 'Stale prompt',
                        'prompt' => 'A prompt that will be removed.',
                    ],
                ],
            ],
        ],
    ]));

    expect(Prompt::find($staleId))->not->toBeNull();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [],
    ]));

    expect(Prompt::find($staleId))->toBeNull();
});

it('does not delete non-smart prompts', function () {
    $prompt = Prompt::factory()->create(['is_smart' => false]);

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [],
    ]));

    expect(Prompt::find($prompt->getKey()))->not->toBeNull();
});

it('rejects malformed smart prompt containers without deleting existing smart prompts', function (mixed $smartPrompts) {
    $existingPrompt = Prompt::factory()->create(['is_smart' => true]);

    expect(fn () => syncTenantRequest([
        'smartPrompts' => $smartPrompts,
    ]))->toThrow(ValidationException::class);

    expect(Prompt::find($existingPrompt->getKey()))->not->toBeNull();
})->with([
    'top level is not an array' => ['invalid'],
    'category is not an array' => [['invalid']],
    'prompt list is not an array' => [[
        [
            'title' => 'Recruitment',
            'smart_prompts' => 'invalid',
        ],
    ]],
    'prompt is not an array' => [[
        [
            'title' => 'Recruitment',
            'smart_prompts' => ['invalid'],
        ],
    ]],
]);

it('throws a validation error when a smart prompt title conflicts with an existing custom prompt in the same category, instead of persisting any changes', function () {
    $promptType = PromptType::factory()->create(['title' => 'Recruitment']);
    Prompt::factory()->create([
        'type_id' => $promptType->getKey(),
        'title' => 'Draft an email',
        'is_smart' => false,
    ]);

    $promptId = fake()->uuid();

    expect(fn () => app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Draft an email',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
        ],
    ])))->toThrow(ValidationException::class);

    expect(Prompt::find($promptId))->toBeNull();
});

it('allows the same prompt title in different categories when title uniqueness is scoped per type', function () {
    $existingPromptType = PromptType::factory()->create(['title' => 'Retention']);
    Prompt::factory()->create([
        'type_id' => $existingPromptType->getKey(),
        'title' => 'Draft an email',
        'is_smart' => false,
    ]);

    $promptId = fake()->uuid();

    app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => $promptId,
                        'title' => 'Draft an email',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
        ],
    ]));

    expect(Prompt::find($promptId))->not->toBeNull();
});

it('rejects a prompt title that conflicts with a custom prompt in another category while per-type uniqueness is inactive', function () {
    PromptTitleUniquePerTypeFeature::deactivate();

    $existingPromptType = PromptType::factory()->create(['title' => 'Retention']);
    Prompt::factory()->create([
        'type_id' => $existingPromptType->getKey(),
        'title' => 'Draft an email',
        'is_smart' => false,
    ]);

    expect(fn () => app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => fake()->uuid(),
                        'title' => 'Draft an email',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
        ],
    ])))->toThrow(ValidationException::class);
});

it('rejects duplicate incoming prompt titles across categories while per-type uniqueness is inactive', function () {
    PromptTitleUniquePerTypeFeature::deactivate();

    expect(fn () => app(SyncTenantSmartPrompts::class)->execute(syncTenantRequest([
        'smartPrompts' => [
            [
                'title' => 'Recruitment',
                'smart_prompts' => [
                    [
                        'id' => fake()->uuid(),
                        'title' => 'Draft an email',
                        'prompt' => 'Write an email to a prospect.',
                    ],
                ],
            ],
            [
                'title' => 'Retention',
                'smart_prompts' => [
                    [
                        'id' => fake()->uuid(),
                        'title' => 'DRAFT AN EMAIL',
                        'prompt' => 'Write an email to a student.',
                    ],
                ],
            ],
        ],
    ])))->toThrow(ValidationException::class);
});
