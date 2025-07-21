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
import DOMPurify from 'dompurify';
import { marked } from 'marked';

document.addEventListener('alpine:init', () => {
    Alpine.data(
        'results',
        ({
            researchRequestId,
            results,
            outline,
            sources,
            files,
            links,
            searchQueries,
            parsedFiles,
            parsedLinks,
            parsedSearchResults,
            title,
            isFinished,
        }) => ({
            reasoningPoints: [],

            results,

            pendingResults: '',

            resultsHtml: '',

            sourcesHtml: [],

            isFinished,

            title,

            outline,

            sources,

            files,

            links,

            searchQueries,

            parsedFiles,

            parsedLinks,

            parsedSearchResults,

            init: async function () {
                Echo.private(`research-request-${researchRequestId}`)
                    .listen('.research-request.file-parsed', (event) => {
                        this.parsedFiles.push(event.parsed_file);

                        this.renderReasoningPoints();
                        this.renderSourcesHtml();
                    })
                    .listen('.research-request.finished', (event) => {
                        this.title = event.title;
                        this.isFinished = true;
                    })
                    .listen('.research-request.link-parsed', (event) => {
                        this.parsedLinks.push(event.parsed_link);

                        this.renderReasoningPoints();
                        this.renderSourcesHtml();
                    })
                    .listen('.research-request.outline-generated', (event) => {
                        this.outline = event.outline;

                        this.renderReasoningPoints();
                    })
                    .listen('.research-request.results-generated', (event) => {
                        if (
                            event.results_chunk === null ||
                            event.results_chunk === undefined ||
                            event.results_chunk === ''
                        ) {
                            return;
                        }

                        this.pendingResults += event.results_chunk;
                    })
                    .listen('.research-request.search-queries-generated', (event) => {
                        this.searchQueries = event.search_queries;

                        this.renderReasoningPoints();
                    })
                    .listen('.research-request.search-results-parsed', (event) => {
                        this.parsedSearchResults.push(event.parsed_search_results);
                        this.sources = [...(this.sources ?? []), ...event.new_sources];

                        this.renderReasoningPoints();
                        this.renderSourcesHtml();
                    });

                const interval = setInterval(() => {
                    if (this.isFinished) {
                        clearInterval(interval);

                        this.results += this.pendingResults;
                        this.pendingResults = '';
                        this.renderResults();

                        return;
                    }

                    if (/^\s*$/.test(this.pendingResults)) {
                        return;
                    }

                    const maxChunks = 3;
                    let chunks = [];
                    let pendingResults = this.pendingResults;

                    const regex = /^(\s*\S+)/;

                    while (chunks.length < maxChunks) {
                        const match = pendingResults.match(regex);
                        if (!match) break;

                        const chunk = match[0];
                        chunks.push(chunk);
                        pendingResults = pendingResults.slice(chunk.length);
                    }

                    if (chunks.length > 0) {
                        const combined = chunks.join('');
                        this.results += combined;
                        this.pendingResults = this.pendingResults.slice(combined.length);
                        this.renderResults();
                    }
                }, 100);

                this.renderReasoningPoints();
                this.renderResults();
                this.renderSourcesHtml();
            },

            renderReasoningPoints: function () {
                let reasoningPoints = ['Started researching the topic...'];

                this.files.forEach((file) => {
                    reasoningPoints.push(`Started reading file: [${file.name}](${file.temporary_url})`);
                });

                this.links?.forEach((link) => {
                    reasoningPoints.push(`Started reading link: [${link}](${link})`);
                });

                const parsingReasoningPoints = [];

                this.parsedFiles.forEach((file) => {
                    parsingReasoningPoints.push([
                        `Finished reading file: [${file.media.name}](${file.media.temporary_url})`,
                        file.created_at,
                    ]);
                });

                this.parsedLinks.forEach((link) => {
                    parsingReasoningPoints.push([`Finished reading link: [${link.url}](${link.url})`, link.created_at]);
                });

                reasoningPoints = [
                    ...reasoningPoints,
                    ...parsingReasoningPoints.sort((a, b) => new Date(a[1]) - new Date(b[1])).map((point) => point[0]),
                ];

                if (
                    (this.files.length && this.files.length === this.parsedFiles.length) ||
                    (this.links?.length && this.links?.length === this.parsedLinks.length)
                ) {
                    reasoningPoints.push('Uploading knowledge and checking for missing information to search for...');
                }

                if (!this.files.length || !this.links?.length) {
                    reasoningPoints.push('Checking for missing information to search for...');
                }

                if (this.searchQueries) {
                    this.searchQueries.forEach((searchQuery) => {
                        reasoningPoints.push(
                            `Started searching the web: ["${searchQuery}"](https://google.com/search?q=${encodeURIComponent(searchQuery.search_query)})`,
                        );
                    });
                }

                this.parsedSearchResults
                    .sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
                    .forEach((searchResults) => {
                        reasoningPoints.push(
                            `Finished searching the web: ["${searchResults.search_query}"](https://google.com/search?q=${encodeURIComponent(searchResults.search_query)})`,
                        );
                    });

                if (this.searchQueries && this.searchQueries?.length === this.parsedSearchResults.length) {
                    reasoningPoints.push('Generating a research outline based on all the information gathered...');
                }

                if (this.outline) {
                    reasoningPoints.push('Preparing to write the report...');
                }

                if (this.isFinished) {
                    reasoningPoints.push('Finished writing the report.');
                }

                this.reasoningPoints = reasoningPoints.map((point) =>
                    DOMPurify.sanitize(marked.parse(point)).replace('<a', '<a target="_blank" rel="noreferrer" '),
                );
            },

            renderResults: function () {
                if (this.results === null || this.results === undefined || this.results?.trim() === '') {
                    this.resultsHtml = '';

                    return;
                }

                const unsafeHtml = marked.parse(this.results);

                this.resultsHtml = DOMPurify.sanitize(unsafeHtml)
                    .replace('<a', '<a target="_blank" rel="noreferrer" ')
                    .replace(
                        '<h2 class="sr-only" id="footnote-label">Footnotes</h2>',
                        '<h2 id="footnote-label">References</h2>',
                    );
            },

            renderSourcesHtml: function () {
                let sourcesHtml = [];

                this.parsedFiles.forEach((file) => {
                    this.sourcesHtml.push(`File: [${file.media.name}](${file.media.temporary_url})`);
                });

                this.parsedLinks.forEach((link) => {
                    this.sourcesHtml.push(`Link: [${link.url}](${link.url})`);
                });

                sourcesHtml = [...sourcesHtml, ...(this.sources ?? [])];

                this.sourcesHtml = sourcesHtml.map((source) =>
                    DOMPurify.sanitize(marked.parse(source)).replace('<a', '<a target="_blank" rel="noreferrer" '),
                );
            },
        }),
    );
});
