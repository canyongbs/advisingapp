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

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Settings\AiSettings;
use App\Models\Tenant;

class CreateThread
{
    public function __invoke(AiApplication $application, ?AiAssistant $assistant = null): AiThread
    {
        $assistant ??= $this->getDefaultAiAssistant($application);

        $existingThread = auth()->user()->aiThreads()
            ->whereNull('name')
            ->whereBelongsTo($assistant, 'assistant')
            ->whereDoesntHave('messages')
            ->first();

        if ($existingThread) {
            return $existingThread;
        }

        $thread = new AiThread();
        $thread->assistant()->associate($assistant);
        $thread->user()->associate(auth()->user());

        $thread->assistant->model->getService()->createThread($thread);

        $thread->save();

        return $thread;
    }

    protected function getDefaultAiAssistant(AiApplication $application): AiAssistant
    {
        $assistant = AiAssistant::query()
            ->where('application', $application)
            ->where('is_default', true)
            ->first();

        if ($assistant) {
            return $assistant;
        }

        $tenant = Tenant::current();
        $settings = app(AiSettings::class);

        $assistant = new AiAssistant();

        if ($application === AiApplication::PersonalAssistant) {
            $assistant->name = 'Institutional Assistant';
            $assistant->description = 'Using the most powerful models available, the primary Institutional Assistant has robust general intelligence, and is designed to serve your college or university.';
        } else {
            $assistant->name = "{$tenant->name} AI Assistant";
            $assistant->description = "An AI Assistant for {$tenant->name}";
        }
        $assistant->instructions = $settings->prompt_system_context;
        $assistant->application = $application;
        $assistant->model = $application->getDefaultModel();
        $assistant->is_default = true;

        $assistant->model->getService()->createAssistant($assistant);

        $assistant->save();

        return $assistant;
    }
}
