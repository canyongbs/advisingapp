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
    Alpine.data('qnaAdvisorPreview', ({ csrfToken, sendMessageUrl, userId }) => ({
        error: null,
        isSendingMessage: false,
        message: '',
        rawIncomingResponse: '',
        messages: [],
        nextRequestOptions: null,

        init: async function () {
            this.render();

            setInterval(this.render.bind(this), 500);
        },

        handleMessageResponse: async function ({ response }) {
            if (!response.ok) {
                const responseJson = await response.json();

                this.error = responseJson.message;
                this.isSendingMessage = false;

                return;
            }

            this.nextRequestOptions = null;

            const responseReader = response.body.pipeThrough(new TextDecoderStream()).getReader();

            const readResponse = async () => {
                const { done, value } = await responseReader.read();

                if (done) {
                    return;
                }

                this.parseEvents(value).forEach((event) => {
                    if (event.type === 'content') {
                        this.error = null;

                        this.rawIncomingResponse += new TextDecoder().decode(
                            Uint8Array.from(atob(event.content), (m) => m.codePointAt(0)),
                        );

                        this.messages[this.messages.length - 1].content = DOMPurify.sanitize(
                            marked.parse(this.rawIncomingResponse),
                        );
                    }

                    if (event.type === 'next_request_options') {
                        this.nextRequestOptions = JSON.parse(
                            new TextDecoder().decode(Uint8Array.from(atob(event.options), (m) => m.codePointAt(0))),
                        );
                    }
                });

                await readResponse();
            };

            this.messages.push({
                content: '',
            });

            this.rawIncomingResponse = '';

            await readResponse();

            this.isSendingMessage = false;
        },

        sendMessage: async function () {
            if (!this.message.replace(/\s/g, '').length) {
                // The message is empty / whitespace only.

                return;
            }

            this.isSendingMessage = true;
            this.error = null;

            const message = this.message;

            this.messages.push({
                content: this.message.replace(/(?:\r\n|\r|\n)/g, '<br />'),
                user_id: userId,
            });

            this.message = '';

            this.$nextTick(async () => {
                await this.handleMessageResponse({
                    response: await fetch(sendMessageUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            content: message,
                            options: this.nextRequestOptions,
                        }),
                    }),
                });
            });
        },

        render: function () {
            if (!this.$refs.messageInput) {
                return;
            }

            if (this.$refs.messageInput.scrollHeight > 0) {
                this.$refs.messageInput.style.height = '5rem';
                this.$refs.messageInput.style.height = `min(${this.$refs.messageInput.scrollHeight}px, 25dvh)`;
            }
        },

        parseEvents: function (encodedEvents) {
            encodedEvents = encodedEvents
                .split('\n')
                .map((l) => l.trim())
                .join('');

            let jsonObjectIndex = encodedEvents.indexOf('{');

            let openJsonObjects = 0;

            const events = [];

            for (let i = jsonObjectIndex; i < encodedEvents.length; i++) {
                if (encodedEvents[i] === '{' && (i < 2 || encodedEvents.slice(i - 2, i) !== '\\"')) {
                    openJsonObjects++;

                    if (openJsonObjects === 1) {
                        jsonObjectIndex = i;
                    }
                } else if (encodedEvents[i] === '}' && (i < 2 || encodedEvents.slice(i - 2, i) !== '\\"')) {
                    openJsonObjects--;

                    if (openJsonObjects === 0) {
                        events.push(JSON.parse(encodedEvents.substring(jsonObjectIndex, i + 1)));

                        jsonObjectIndex = i + 1;
                    }
                }
            }

            return events;
        },
    }));
});
