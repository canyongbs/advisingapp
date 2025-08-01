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
@php
    use Illuminate\Support\Str;
@endphp

<div>
    @if ($researchRequest)
        <section
            class="prose max-w-none dark:prose-invert"
            x-data="results({
                researchRequestId: @js($researchRequest->getKey()),
                results: @js($researchRequest->results ?? ''),
                outline: @js($researchRequest->outline),
                sources: @js($researchRequest->sources),
                files: @js(
                    $researchRequest->getMedia('files')->map(fn($media) => data_set($media, 'temporary_url', $media->getTemporaryUrl(now()->addDay())))->toArray()
                ),
                links: @js($researchRequest->links),
                searchQueries: @js($researchRequest->search_queries),
                parsedFiles: @js(
                    $researchRequest->parsedFiles->loadMissing(['media'])->map(fn($file) => data_set($file, 'media.temporary_url', $file->media->getTemporaryUrl(now()->addDay())))->map(fn($file): array => Arr::except($file->toArray(), ['results']))->toArray()
                ),
                parsedLinks: @js($researchRequest->parsedLinks->map(fn($link): array => Arr::except($link->toArray(), ['results']))->toArray()),
                parsedSearchResults: @js($researchRequest->parsedSearchResults->map(fn($searchResults): array => Arr::except($searchResults->toArray(), ['results']))->toArray()),
                title: @js($researchRequest->title),
                isFinished: @js((bool) $researchRequest->finished_at),
            })"
            wire:key="{{ Str::random() }}"
        >
            <div x-show="! reasoningPoints.length">
                Sending request to the research assistant...
            </div>

            <details
                class="research-request-reasoning"
                open
                x-show="reasoningPoints.length > 0"
            >
                <summary class="cursor-pointer">Reasoning</summary>

                <div
                    class="flex h-20 items-start overflow-y-auto px-4 text-xs tracking-tight shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10"
                    x-bind:class="{
                        'flex-col-reverse': !isFinished,
                    }"
                >
                    <ul>
                        <template
                            x-for="(point, index) in reasoningPoints"
                            :key="index"
                        >
                            <li x-html="point"></li>
                        </template>
                    </ul>
                </div>
            </details>

            <div
                class="mx-1 mb-12 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                x-show="resultsHtml.length > 0"
            >
                <h1
                    x-text="title"
                    x-show="title"
                ></h1>

                <div x-html="resultsHtml"></div>

                <p
                    class="text-gray-600 dark:text-gray-400"
                    x-show="outline && (! isFinished)"
                >
                    Writing report...
                </p>

                <h2 x-show="isFinished && (sourcesHtml.length > 0)">
                    Sources:
                </h2>

                <ul x-show="isFinished && (sourcesHtml.length > 0)">
                    <template
                        x-for="(source, index) in sourcesHtml"
                        :key="index"
                    >
                        <li x-html="source"></li>
                    </template>
                </ul>

                @if ($showEmailResults)
                    <section class="mt-3 px-3 text-right">
                        {{ ($this->emailResearchRequestAction)(['researchRequest' => $researchRequest->getKey()]) }}
                    </section>
                @endif
            </div>
        </section>
    @endif

    @vite('app-modules/research/resources/js/results.js')
</div>
