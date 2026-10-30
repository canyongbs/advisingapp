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

namespace AdvisingApp\Ai\Services\LlamaParse;

use AdvisingApp\Ai\DataTransferObjects\LlamaParseV2ParseJobData;
use AdvisingApp\Ai\Enums\LlamaParseV2JobStatus;
use AdvisingApp\Ai\Enums\LlamaParseV2Tier;
use AdvisingApp\Ai\Exceptions\LlamaParseApiKeyNotConfiguredException;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

class LlamaParseV2Client
{
    /**
     * The parser version every parse job is pinned to.
     *
     * LlamaParse recommends locking to a dated version so that output stays consistent, and only
     * upgrading as new versions come out and have been tested. Never use `latest` in production.
     *
     * The versions each tier accepts are listed by the List Parse Versions endpoint
     * (GET https://api.cloud.llamaindex.ai/api/v2/parse/versions). This value was retrieved from it,
     * and is valid for the `cost_effective`, `agentic` and `agentic_plus` tiers, but not for `fast`.
     * Check that a tier accepts this version before adding it to `LlamaParseV2Tier`.
     *
     * @see https://developers.llamaindex.ai/reference/resources/parsing/methods/list_versions/
     * @see https://developers.llamaindex.ai/llamaparse/parse/guides/tiers/
     */
    public const string VERSION = '2026-08-19';

    protected const string BASE_URL = 'https://api.cloud.llamaindex.ai/api/v2';

    protected const int TIMEOUT_SECONDS = 30;

    protected const int CONNECT_TIMEOUT_SECONDS = 10;

    public function __construct(
        protected AiIntegrationsSettings $aiIntegrationsSettings,
    ) {}

    /**
     * Submits a parse job and returns its ID. The tier and the pinned version always take
     * precedence over any `tier` or `version` passed in the parameters.
     *
     * @param array<string, mixed> $parameters The other create parameters, such as `source_url` or `file_id`.
     *
     * @see https://developers.llamaindex.ai/reference/resources/parsing/methods/create/
     */
    public function createParseJob(LlamaParseV2Tier $tier, array $parameters): string
    {
        $jobId = $this->request()
            ->post('parse', [
                ...$parameters,
                'tier' => $tier->value,
                'version' => self::VERSION,
            ])
            ->throw()
            ->json('id');

        if ((! is_string($jobId)) || blank($jobId)) {
            throw new UnexpectedValueException('LlamaParse did not return a parse job ID.');
        }

        return $jobId;
    }

    /**
     * @param array<string> $expand The content to include alongside the job, such as `markdown_full`.
     *
     * @see https://developers.llamaindex.ai/reference/resources/parsing/methods/get/
     */
    public function getParseJob(string $jobId, array $expand = []): LlamaParseV2ParseJobData
    {
        $response = $this->request()
            ->get(
                'parse/' . rawurlencode($jobId),
                collect($expand)
                    ->map(fn (string $value): string => 'expand=' . rawurlencode($value))
                    ->implode('&'),
            )
            ->throw();

        $id = $response->json('job.id');

        if ((! is_string($id)) || blank($id)) {
            throw new UnexpectedValueException("LlamaParse did not return the parse job [{$jobId}].");
        }

        $status = LlamaParseV2JobStatus::tryFrom((string) $response->json('job.status'))
            ?? throw new UnexpectedValueException("LlamaParse returned an unknown status for the parse job [{$jobId}].");

        $errorMessage = $response->json('job.error_message');
        $markdownFull = $response->json('markdown_full');

        return new LlamaParseV2ParseJobData(
            id: $id,
            status: $status,
            errorMessage: is_string($errorMessage) ? $errorMessage : null,
            markdownFull: is_string($markdownFull) ? $markdownFull : null,
        );
    }

    protected function request(): PendingRequest
    {
        $apiKey = $this->aiIntegrationsSettings->llamaparse_api_key;

        if (blank($apiKey)) {
            throw new LlamaParseApiKeyNotConfiguredException();
        }

        return Http::baseUrl(self::BASE_URL)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout(self::TIMEOUT_SECONDS)
            ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS);
    }
}
