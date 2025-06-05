{{--
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
--}}
<div @if ($researchRequest?->hasStarted() && !$researchRequest?->finished_at) wire:poll.3s @endif>
    @if (!$researchRequest?->finished_at)
        <div class="flex items-center gap-2 mb-4">
            <x-filament::loading-indicator class="h-5 w-5" /> Researching...
        </div>
    @endif

    <section
        class="prose max-w-none dark:prose-invert"
        x-data="results"
    >
        <input
            type="hidden"
            value="{{ $researchRequest?->hasStarted() && !$researchRequest?->finished_at ? 1 : 0 }}"
            x-ref="isStreamingInput"
        />
        <input
            type="hidden"
            value="{{ base64_encode($researchRequest?->results) }}"
            x-ref="markdownInput"
        />

        <details
            class="research-request-reasoning"
            x-show="reasoningHtml"
            @if ($researchRequest?->hasStarted() && !$researchRequest?->finished_at ? 1 : 0)
                open 
            @endif
        >
            <summary class="cursor-pointer">Reasoning</summary>

            <div
                @class([
                    'flex h-20 overflow-y-auto text-xs tracking-tight shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 px-4 items-start',
                    'flex-col-reverse' => $researchRequest?->hasStarted() && !$researchRequest?->finished_at ? 1 : 0
                ])
                x-html="reasoningHtml"
            >
            </div>
        </details>

        <div
            x-show="resultsHtml.length > 0"
            class="mx-1 mb-12 p-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        >
            @if (filled($researchRequest?->title))
                <h1>{{ $researchRequest->title }}</h1>
            @endif

            <div x-html="resultsHtml"></div>
        </div>
    </section>

    <script src="{{ url('js/canyon-gbs/research/results.js') . '?v=' . app('current-commit') }}"></script>
</div>
