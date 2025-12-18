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

use AdvisingApp\Ai\DataTransferObjects\FileParsingResult;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UploadFileForParsing
{
    public function __construct(
        protected AiIntegrationsSettings $aiIntegrationsSettings,
    ) {}

    public function execute(string $path, string $name, string $mimeType): FileParsingResult
    {
        if ($this->isPlainTextFile($name, $mimeType)) {
            return new FileParsingResult(
                fileId: null,
                parsingResults: $this->readFileContent($path),
            );
        }

        /** @var AwsS3V3Adapter $s3Adapter */
        $s3Adapter = Storage::disk('s3')->getAdapter();

        invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
        $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $path)->replace('\\', '/');

        $resource = fopen($fileS3Path, mode: 'r', context: stream_context_create([
            's3' => [
                'seekable' => true,
            ],
        ]));

        $data = [
            'parse_mode' => $this->isImage($mimeType) ? 'parse_page_with_lvm' : 'parse_page_with_llm',
            'do_not_cache' => true,
            'user_prompt' => 'If the upload has images retrieve text from it and also describe the image in detail. If the upload seems to be just an image with no text in it, just return the image description.',
        ];

        try {
            $response = Http::attach(
                'file',
                $resource,
                $name,
                ['Content-Type' => $mimeType]
            )
                ->retry(2)
                ->withToken($this->aiIntegrationsSettings->llamaparse_api_key)
                ->acceptJson()
                ->post('https://api.cloud.llamaindex.ai/api/v1/parsing/upload', $data)
                ->throw();
        } catch (Throwable $exception) {
            report($exception);

            return new FileParsingResult(
                fileId: null,
                parsingResults: null,
            );
        }

        if ((! $response->successful()) || blank($response->json('id'))) {
            return new FileParsingResult(
                fileId: null,
                parsingResults: null,
            );
        }

        return new FileParsingResult(
            fileId: $response->json('id'),
            parsingResults: null,
        );
    }

    protected function isPlainTextFile(string $name, string $mimeType): bool
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return in_array($extension, ['txt', 'md', 'csv'], true)
            || in_array($mimeType, [
                'text/plain',
                'text/markdown',
                'text/csv',
                'text/x-csv',
                'text/comma-separated-values',
                'application/csv',
                'application/x-csv',
            ], true);
    }

    protected function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    protected function readFileContent(string $path): ?string
    {
        if (str_starts_with($path, '/')) {
            return file_get_contents($path) ?: null;
        }

        /** @var AwsS3V3Adapter $s3Adapter */
        $s3Adapter = Storage::disk('s3')->getAdapter();

        invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
        $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $path)->replace('\\', '/');

        return file_get_contents($fileS3Path) ?: null;
    }
}
