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
use AdvisingApp\KnowledgeBase\Exceptions\KnowledgeBaseItemExternalMediaContentValidationException;

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

            $bucket = $diskConfig['bucket'] ?? null;
            $bucketPath = $bucket ? "/{$bucket}/" : '';

            $path = parse_url($content, PHP_URL_PATH);

            if (! $path) {
                $path = '';
            }

            $path = Str::of($path)->replaceFirst($bucketPath, '');

            if (! Storage::disk($disk)->exists($path)) {
                try {
                    if (! $stream = @fopen($content, 'r')) {
                        throw new Exception('Unable to open stream for ' . $content);
                    }

                    $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');

                    file_put_contents($tempFile, $stream);

                    $tmpFile = new UploadedFile($tempFile, basename($content));

                    if (! in_array($tmpFile->getMimeType(), config('filament-tiptap-editor.accepted_file_types'))) {
                        throw new KnowledgeBaseItemExternalMediaContentValidationException('The file type is not allowed.');
                    }

                    if (($tmpFile->getSize() / 1000) > config('filament-tiptap-editor.max_file_size')) {
                        throw new KnowledgeBaseItemExternalMediaContentValidationException('The file size is too large.');
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
