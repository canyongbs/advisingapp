<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Jobs;

use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Throwable;

class GenerateAvatar implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public int $tries = 3;

    public function __construct(
        public Model $record,
        public string $instructions,
        public string $mediaCollection,
    ) {}

    public function handle(): void
    {
        $service = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

        if (! $service->hasImageGeneration()) {
            throw new Exception('Image generation is not enabled.');
        }

        $record = $this->record;

        assert($record instanceof HasMedia);

        if (! method_exists($record, 'addMediaFromBase64')) {
            throw new Exception('The provided model does not support media uploads.');
        }

        try {
            $image = $service->image(
                prompt: Str::limit($this->instructions, limit: 1000) . PHP_EOL . PHP_EOL . ' Create an avatar image using these instructions. The image should be square and professionally appropriate for use as a profile picture.',
            );

            DB::transaction(function () use ($image, $record) {
                $record->clearMediaCollection($this->mediaCollection);
                $record->addMediaFromBase64($image)
                    ->usingFileName(Str::random() . '.jpg')
                    ->toMediaCollection($this->mediaCollection);
            });
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
