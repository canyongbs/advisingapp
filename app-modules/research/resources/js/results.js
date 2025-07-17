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
import markedFootnote from 'marked-footnote';

document.addEventListener('alpine:init', () => {
    Alpine.data('results', ({ results, parsedFiles, parsedLinks, parsedSearchResults, title, isFinished }) => ({
        reasoningPoints: [],

        resultsMarkdown: results,

        resultsHtml: '',

        isFinished,

        title,

        init: async function () {
            this.renderReasoningPoints();
            this.renderResults();
        },

        renderReasoningPoints: function () {
            const reasoningPoints = [];

            parsedFiles.forEach((file) => {
                reasoningPoints.push([`File: ${file.media.name}`, file.created_at]);
            });

            parsedLinks.forEach((link) => {
                reasoningPoints.push([`Link: ${link.url}`, link.created_at]);
            });

            parsedSearchResults.forEach((searchResults) => {
                reasoningPoints.push([`Search Results: ${searchResults.search_query}`, searchResults.created_at]);
            });

            this.reasoningPoints = reasoningPoints
                .sort((a, b) => new Date(a[1]) - new Date(b[1]))
                .map((point) => point[0]);
        },

        renderResults: function () {
            let unsafeHtml = marked.use(markedFootnote()).parse(this.resultsMarkdown);

            this.resultsHtml = DOMPurify.sanitize(unsafeHtml).replace(
                '<h2 class="sr-only" id="footnote-label">Footnotes</h2>',
                '<h2 id="footnote-label">References</h2>',
            );
        },
    }));
});
