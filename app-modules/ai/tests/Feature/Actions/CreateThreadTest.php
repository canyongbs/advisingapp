<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use App\Models\Tenant;
use Mockery\MockInterface;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Ai\Actions\CreateThread;
use AdvisingApp\Ai\Services\TestAiService;

it('creates a new thread', function () {
    asSuperAdmin();

    /** @phpstan-ignore-next-line */
    $this->mock(
        TestAiService::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('createThread')->once(),
    );

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $thread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($thread)
        ->assistant->toBe($assistant)
        ->user->toBe(auth()->user())
        ->wasRecentlyCreated->toBeTrue();
});

it('does not create a new thread if an empty existing one exists', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $existingThread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($existingThread->getKey())
        ->toBe($thread->getKey());
});

it('does not match existing threads belonging to other users', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(User::factory()->create())
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('does not match existing threads with messages', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->count(1), 'messages')
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('does not match existing threads belonging to other assistants', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('does not match existing saved threads (with a name)', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create([
            'name' => 'Test',
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant, $assistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('uses the default assistant if none is provided', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $thread = app(CreateThread::class)(AiApplication::PersonalAssistant);

    expect($thread)
        ->assistant->getKey()->toBe($assistant->getKey())
        ->user->toBe(auth()->user())
        ->wasRecentlyCreated->toBeTrue();
});

it('does not match a default assistant if one belongs to a different application', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::ReportAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('does not match an assistant if it is not marked as default', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => false,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create([
            'name' => null,
        ]);

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $newThread = app(CreateThread::class)(AiApplication::PersonalAssistant);

    expect($newThread->getKey())
        ->not->toBe($thread->getKey());
});

it('creates a new assistant if no default assistant exists', function () {
    asSuperAdmin();

    /** @phpstan-ignore-next-line */
    $this->mock(
        TestAiService::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('createAssistant', 'createThread')->once(),
    );

    $settings = app(AiSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();

    $thread = app(CreateThread::class)(AiApplication::PersonalAssistant);

    $tenant = Tenant::current();

    expect($thread->assistant)
        ->name->toBe("{$tenant->name} AI Assistant")
        ->description->toBe("An AI Assistant for {$tenant->name}")
        ->instructions->toBe($settings->prompt_system_context)
        ->application->toBe(AiApplication::PersonalAssistant)
        ->model->toBe($settings->getDefaultModel())
        ->is_default->toBeTrue()
        ->wasRecentlyCreated->toBeTrue();
});
