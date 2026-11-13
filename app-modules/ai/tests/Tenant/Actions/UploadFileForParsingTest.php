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

use AdvisingApp\Ai\Actions\GetLlamaParseWebhookParameters;
use AdvisingApp\Ai\Actions\UploadFileForParsing;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseFileParsingFeature;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;

$makeAction = function (): UploadFileForParsing {
    $stream = fopen('php://memory', 'r+');

    assert(is_resource($stream));

    fwrite($stream, '%PDF-1.4 Course syllabus');
    rewind($stream);

    // The file is read from S3, which is not available in tests.
    $action = Mockery::mock(UploadFileForParsing::class, [
        // TODO: Cleanup Task (llamaparse-file-parsing): remove this argument along with the `$aiIntegrationsSettings` constructor property of `UploadFileForParsing`.
        app(AiIntegrationsSettings::class),
        app(LlamaParseV2Client::class),
        app(GetLlamaParseWebhookParameters::class),
    ])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $action->shouldReceive('openFileStream')->andReturn($stream);

    assert($action instanceof UploadFileForParsing);

    return $action;
};

$getUploadedConfiguration = function (Request $request): array {
    $configuration = json_decode(collect($request->data())->firstWhere('name', 'configuration')['contents'] ?? '', associative: true);

    assert(is_array($configuration));

    return $configuration;
};

beforeEach(function (): void {
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

it('uploads the file to LlamaParse V2 and returns the parse job ID', function (string $mimeType, string $tier) use ($makeAction, $getUploadedConfiguration): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/upload' => Http::response(['id' => 'pjb-123']),
    ]);

    $result = $makeAction()->execute('files/syllabus', 'syllabus', $mimeType);

    expect($result->fileId)->toBe('pjb-123')
        ->and($result->parsingResults)->toBeNull();

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) use ($getUploadedConfiguration, $tier): bool {
        expect($request->url())->toBe('https://api.cloud.llamaindex.ai/api/v2/parse/upload')
            ->and($request->hasFile('file', filename: 'syllabus'))->toBeTrue()
            ->and($getUploadedConfiguration($request))->toBe([
                'disable_cache' => true,
                'agentic_options' => [
                    'custom_prompt' => 'If the upload has images retrieve text from it and also describe the image in detail. If the upload seems to be just an image with no text in it, just return the image description.',
                ],
                ...app(GetLlamaParseWebhookParameters::class)(),
                'tier' => $tier,
                'version' => LlamaParseV2Client::VERSION,
            ]);

        return true;
    });
})->with([
    'document' => ['application/pdf', 'cost_effective'],
    'image' => ['image/png', 'agentic'],
]);

it('reports a failed upload and returns no parse job ID', function () use ($makeAction): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/upload' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $result = $makeAction()->execute('files/syllabus.pdf', 'syllabus.pdf', 'application/pdf');

    expect($result->fileId)->toBeNull()
        ->and($result->parsingResults)->toBeNull();

    Exceptions::assertReported(RequestException::class);
});

it('reads plain-text files directly instead of uploading them to LlamaParse', function (): void {
    Http::fake();

    $path = tempnam(sys_get_temp_dir(), 'notes');

    assert(is_string($path));

    file_put_contents($path, 'Office hours are on Tuesdays.');

    $result = app(UploadFileForParsing::class)->execute($path, 'notes.txt', 'text/plain');

    unlink($path);

    expect($result->fileId)->toBeNull()
        ->and($result->parsingResults)->toBe('Office hours are on Tuesdays.');

    Http::assertNothingSent();
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('uploads the file to LlamaParse V1 while LlamaParse file parsing is inactive', function () use ($makeAction): void {
    LlamaParseFileParsingFeature::deactivate();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v1/parsing/upload' => Http::response(['id' => '3b0b1f7e-2c6a-4f43-9d5f-1d2a2b7f9c10']),
    ]);

    $result = $makeAction()->execute('files/syllabus.pdf', 'syllabus.pdf', 'application/pdf');

    expect($result->fileId)->toBe('3b0b1f7e-2c6a-4f43-9d5f-1d2a2b7f9c10');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloud.llamaindex.ai/api/v1/parsing/upload'
        && $request->hasFile('file', filename: 'syllabus.pdf')
        && (collect($request->data())->firstWhere('name', 'tier')['contents'] ?? null) === 'cost_effective');
});
