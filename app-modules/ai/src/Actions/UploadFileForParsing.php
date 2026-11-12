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

use AdvisingApp\Ai\DataTransferObjects\FileParsingResult;
use AdvisingApp\Ai\Enums\LlamaParseV2Tier;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseFileParsingFeature;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Throwable;

class UploadFileForParsing
{
    protected const string PARSING_PROMPT = 'If the upload has images retrieve text from it and also describe the image in detail. If the upload seems to be just an image with no text in it, just return the image description.';

    public function __construct(
        protected AiIntegrationsSettings $aiIntegrationsSettings,
        protected LlamaParseV2Client $llamaParseV2Client,
        protected GetLlamaParseWebhookParameters $getLlamaParseWebhookParameters,
    ) {}

    public function execute(string $path, string $name, string $mimeType): FileParsingResult
    {
        if ($this->isPlainTextFile($name, $mimeType)) {
            return new FileParsingResult(
                fileId: null,
                parsingResults: $this->readFileContent($path),
            );
        }

        $resource = $this->openFileStream($path);

        try {
            $fileId = LlamaParseFileParsingFeature::active()
                ? $this->uploadToLlamaParse($resource, $name, $mimeType)
                // TODO: Cleanup Task (llamaparse-file-parsing): delete this V1 upload, along with `uploadToLlamaParseV1()`, the `$aiIntegrationsSettings` constructor property it uses, and that argument in `UploadFileForParsingTest`.
                : $this->uploadToLlamaParseV1($resource, $name, $mimeType);
        } catch (Throwable $exception) {
            report($exception);

            return new FileParsingResult(
                fileId: null,
                parsingResults: null,
            );
        }

        return new FileParsingResult(
            fileId: $fileId,
            parsingResults: null,
        );
    }

    /**
     * @return resource|false
     */
    protected function openFileStream(string $path): mixed
    {
        $s3Adapter = Storage::disk('s3')->getAdapter();

        assert($s3Adapter instanceof AwsS3V3Adapter);

        invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
        $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $path)->replace('\\', '/');

        return fopen($fileS3Path, mode: 'r', context: stream_context_create([
            's3' => [
                'seekable' => true,
            ],
        ]));
    }

    /**
     * Uploads the file and submits it for parsing in the same request, returning the parse job's ID. The job registers
     * the webhook, which LlamaParse calls once the job finishes, and the job is also checked on regularly until then.
     *
     * @param resource|false $resource
     */
    protected function uploadToLlamaParse(mixed $resource, string $name, string $mimeType): string
    {
        assert(is_resource($resource));

        return $this->llamaParseV2Client->uploadFile(
            $this->isImage($mimeType) ? LlamaParseV2Tier::Agentic : LlamaParseV2Tier::CostEffective,
            $resource,
            $name,
            $mimeType,
            [
                // Always parse the file rather than returning a cached result, as V1 did with `do_not_cache`.
                'disable_cache' => true,
                'agentic_options' => [
                    'custom_prompt' => self::PARSING_PROMPT,
                ],
                ...($this->getLlamaParseWebhookParameters)(),
            ],
        );
    }

    /**
     * @param resource|false $resource
     */
    protected function uploadToLlamaParseV1(mixed $resource, string $name, string $mimeType): ?string
    {
        $response = Http::attach(
            'file',
            $resource,
            $name,
            ['Content-Type' => $mimeType]
        )
            ->withToken($this->aiIntegrationsSettings->llamaparse_api_key)
            ->acceptJson()
            ->post('https://api.cloud.llamaindex.ai/api/v1/parsing/upload', [
                'tier' => $this->isImage($mimeType) ? 'agentic' : 'cost_effective',
                'version' => 'latest',
                'do_not_cache' => true,
                'user_prompt' => self::PARSING_PROMPT,
            ])
            ->throw();

        $fileId = $response->json('id');

        return (is_string($fileId) && filled($fileId)) ? $fileId : null;
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

        $s3Adapter = Storage::disk('s3')->getAdapter();

        assert($s3Adapter instanceof AwsS3V3Adapter);

        invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
        $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $path)->replace('\\', '/');

        return file_get_contents($fileS3Path) ?: null;
    }
}
