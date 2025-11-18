<?php

namespace AdvisingApp\Ai\Jobs\AiAssistants;

use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Spatie\Multitenancy\Jobs\TenantAware;

class FetchAiAssistantLinkParsingResults implements ShouldQueue, TenantAware, ShouldBeUnique
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 60;

    public function __construct(
        protected AiAssistantLink $link,
    ) {}

    public function handle(): void
    {
        if (filled($this->link->parsing_results)) {
            return;
        }

        $response = Http::withToken(app(AiIntegrationsSettings::class)->jina_deepsearch_v1_api_key)
            ->withHeaders([
                'X-Retain-Images' => 'none',
            ])
            ->get("https://r.jina.ai/{$this->link->url}");

        if (! $response->successful()) {
            $this->release();

            return;
        }

        $this->link->parsing_results = $response->body();
        $this->link->save();
    }

    public function uniqueId(): string
    {
        return $this->link->id;
    }
}
