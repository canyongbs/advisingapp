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
    Alpine.data('results', () => ({
        reasoningHtml: null,

        resultsHtml: '',

        resultsMarkdownLength: 0,

        interval: null,

        init: async function () {
            this.resultsHtml = this.render(this.$refs.markdownInput.value);

            let newHtml = null;
            // let typingDelay = null;

            while (this.$refs.isStreamingInput.value) {
                if (this.$refs.markdownInput.value.length > this.resultsMarkdownLength) {
                    // newHtml = this.render(this.$refs.markdownInput.value);

                    const binary = atob(this.$refs.markdownInput.value);
                    const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
                    const markdown = new TextDecoder().decode(bytes);

                    let unsafeHtml = marked.use(markedFootnote()).parse(markdown);

                    let newContent = unsafeHtml.split('</think>')

                    this.reasoningHtml = DOMPurify.sanitize(newContent[0].replace('<think>', ''));

                    this.resultsHtml = DOMPurify.sanitize(newContent[1] ?? '').replace(
                        '<h2 class="sr-only" id="footnote-label">Footnotes</h2>',
                        '<h2 id="footnote-label">References</h2>',
                    );

                    // this.resultsMarkdownLength = this.$refs.markdownInput.value.length;

                    // if (this.resultsHtml.length > newHtml.length) {
                    //     this.resultsHtml = newHtml;
                    // }

                    // this.resultsHtml = newHtml;

                    // Not sure if we need to do this or not?
                    // this.resultsHtml = newHtml.slice(0, this.resultsHtml.length);

                    // let newContent = this.resultsHtml + newHtml.slice(this.resultsHtml.length);

                    // this.resultsHtml += newHtml.slice(this.resultsHtml.length);

                    // typingDelay = Math.ceil(Math.min(10000 / (newHtml.length - this.resultsHtml.length), 40));

                    // this.resultsHtml = newHtml.slice(0, this.resultsHtml.length);

                    // for (let i = this.resultsHtml.length; i < newHtml.length; i++) {
                    //     this.resultsHtml += newHtml[i];

                    //     await new Promise((resolve) => setTimeout(resolve, typingDelay));
                    // }
                }

                await new Promise((resolve) => setTimeout(resolve, 500));
            }
        },

        render: function (base64EncodedMarkdown) {
            const binary = atob(base64EncodedMarkdown);
            const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
            const markdown = new TextDecoder().decode(bytes);

            let unsafeHtml = marked.use(markedFootnote()).parse(markdown);

            if (unsafeHtml.includes('<think>') && unsafeHtml.includes('</think>')) {
                this.reasoningHtml ??= DOMPurify.sanitize(unsafeHtml.split('</think>').shift().replace('<think>', ''));

                unsafeHtml = unsafeHtml.split('</think>')[1] ?? '';
            } else {
                unsafeHtml = unsafeHtml
                    .replace('<think>', '<div class="research-request-reasoning"><p><strong>Reasoning</strong></p>')
                    .replace('</think>', '</div>');
            }

            return DOMPurify.sanitize(unsafeHtml).replace(
                '<h2 class="sr-only" id="footnote-label">Footnotes</h2>',
                '<h2 id="footnote-label">References</h2>',
            );
        },
    }));
});
