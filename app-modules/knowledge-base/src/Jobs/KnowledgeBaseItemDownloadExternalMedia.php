<?php

namespace AdvisingApp\KnowledgeBase\Jobs;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AdvisingApp\KnowledgeBase\Exceptions\KnowledgeBaseExternalMediaFileAccessException;
use AdvisingApp\KnowledgeBase\Exceptions\KnowledgeBaseExternalMediaValidationException;

class KnowledgeBaseItemDownloadExternalMedia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public KnowledgeBaseItem $knowledgeBaseItem) {}

    public function handle(): void
    {
        $content = $this->processContentItem($this->knowledgeBaseItem->article_details);

        $this->knowledgeBaseItem->article_details = $content;

        $this->knowledgeBaseItem::withoutEvents(fn () => $this->knowledgeBaseItem->save());
    }

    public function processContentItem(array $content): array
    {
        if (isset($content['type']) && $content['type'] === 'image') {
            $content['attrs']['src'] = $this->downloadExternalMedia($content['attrs']['src']);

            return $content;
        }

        return collect($content)->map(function ($item) {
            if (is_array($item)) {
                return $this->processContentItem($item);
            }

            return $item;
        })->toArray();
    }

    public function downloadExternalMedia(string $content): string
    {
        if (Str::isUrl($content)) {
            $disk = config('filament-tiptap-editor.disk');

            $diskConfig = Storage::disk($disk)->getConfig();

            $domains = [];

            if (Str::isUrl($diskConfig['url'])) {
                $domains[] = parse_url($diskConfig['url'])['host'];
            }

            if (Str::isUrl($diskConfig['endpoint'])) {
                $domains[] = parse_url($diskConfig['endpoint'])['host'];
            }

            if (! in_array(parse_url($content)['host'], $domains)) {
                try {
                    if (! $stream = @fopen($content, 'r')) {
                        throw new KnowledgeBaseExternalMediaFileAccessException('Unable to open stream for ' . $content);
                    }

                    $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');

                    file_put_contents($tempFile, $stream);

                    $tmpFile = new UploadedFile($tempFile, basename($content));

                    if (! in_array($tmpFile->getMimeType(), config('filament-tiptap-editor.accepted_file_types'))) {
                        throw new KnowledgeBaseExternalMediaValidationException('The file type is not allowed.');
                    }

                    if (($tmpFile->getSize() / 1000) > config('filament-tiptap-editor.max_file_size')) {
                        throw new KnowledgeBaseExternalMediaValidationException('The file size is too large.');
                    }

                    $media = $this->knowledgeBaseItem->addMedia($tmpFile)
                        ->toMediaCollection('article_details');

                    return "{{media|id:{$media->getKey()};}}";
                } catch (Exception $e) {
                    report($e);
                }
            }
        }

        return $content;
    }
}
