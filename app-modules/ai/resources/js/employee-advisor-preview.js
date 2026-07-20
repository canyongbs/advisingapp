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
import DOMPurify from 'dompurify';
import { marked } from 'marked';

document.addEventListener('alpine:init', () => {
    Alpine.data('employeeAdvisorPreview', ({ csrfToken, sendMessageUrl, showThreadUrl, userId, threadId }) => ({
        error: null,
        isLoading: true,
        isSendingMessage: false,
        message: '',
        rawIncomingResponse: '',
        pendingResponse: '',
        messages: [],
        hasSetUpNewMessageForResponse: false,

        init: async function () {
            this.render();

            setInterval(this.render.bind(this), 500);

            // Drain pending WebSocket chunks word-by-word to avoid layout thrash
            setInterval(() => {
                if (/^\s*$/.test(this.pendingResponse)) {
                    return;
                }

                const maxChunks = 2;
                let chunks = [];
                let pendingResponse = this.pendingResponse;
                const regex = /^(\s*\S+)/;

                while (chunks.length < maxChunks) {
                    const match = pendingResponse.match(regex);
                    if (!match) break;
                    const chunk = match[0];
                    chunks.push(chunk);
                    pendingResponse = pendingResponse.slice(chunk.length);
                }

                if (chunks.length > 0) {
                    const combined = chunks.join('');
                    this.rawIncomingResponse += combined;
                    this.pendingResponse = this.pendingResponse.slice(combined.length);

                    if (this.messages.length > 0) {
                        this.messages[this.messages.length - 1].content = DOMPurify.sanitize(
                            marked.parse(this.rawIncomingResponse),
                        );
                    }
                }
            }, 50);

            // Fetch initial messages (empty for a fresh preview thread)
            const showThreadResponse = await fetch(showThreadUrl, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            const thread = await showThreadResponse.json();

            this.messages = thread.messages.map((message) => {
                if (message.content && !message.user_id) {
                    message.content = DOMPurify.sanitize(marked.parse(message.content));
                }

                return message;
            });

            // Subscribe to the thread's private WebSocket channel
            Echo.private(`advisor-thread-${threadId}`)
                .listen('.advisor-message.chunk', (event) => {
                    this.error = null;

                    if (!this.hasSetUpNewMessageForResponse) {
                        this.messages.push({ content: '' });
                        this.rawIncomingResponse = '';
                        this.pendingResponse = '';
                        this.hasSetUpNewMessageForResponse = true;
                    }

                    this.pendingResponse += event.content;
                })
                .listen('.advisor-message.finished', (event) => {
                    if (event.error) {
                        this.error = event.error;
                    }

                    this.isSendingMessage = false;
                    this.hasSetUpNewMessageForResponse = false;
                });

            this.isLoading = false;
        },

        sendMessage: async function () {
            if (!this.message.replace(/\s/g, '').length) {
                return;
            }

            this.isSendingMessage = true;
            this.error = null;
            this.hasSetUpNewMessageForResponse = false;

            const message = this.message;

            this.messages.push({
                content: this.message.replace(/(?:\r\n|\r|\n)/g, '<br />'),
                user_id: userId,
            });

            this.message = '';

            this.$nextTick(async () => {
                const response = await fetch(sendMessageUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        content: message,
                        files: [],
                        has_image_generation: false,
                    }),
                });

                if (!response.ok) {
                    const responseJson = await response.json();

                    this.error = responseJson.message;
                    this.isSendingMessage = false;
                }
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
    }));
});
