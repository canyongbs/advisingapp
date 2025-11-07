<?php

namespace AdvisingApp\Ai\Jobs;

use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
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
    ) {}

    public function handle(): void
    {
        $service = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

        if (! $service->hasImageGeneration()) {
            return;
        }

        $record = $this->record;

        assert($record instanceof HasMedia);

        if (! method_exists($record, 'addMediaFromBase64')) {
            return;
        }

        try {
            $image = $service->image(
                prompt: Str::limit($this->instructions, limit: 1000) . PHP_EOL . PHP_EOL . ' Create an avatar image for using these instructions. The image should be square and professionally appropriate for use as a profile picture.',
            );

            DB::transaction(function () use ($image, $record) {
                $record->clearMediaCollection('avatar');
                $record->addMediaFromBase64($image)
                    ->usingFileName(Str::random() . '.jpg')
                    ->toMediaCollection('avatar');
            });
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
