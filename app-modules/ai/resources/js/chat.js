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

const addImageDownloadButtons = (htmlContent) => {
    if (!htmlContent || typeof htmlContent !== 'string') {
        return htmlContent;
    }

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = htmlContent;
    const images = tempDiv.querySelectorAll('img[data-id]');

    images.forEach((image) => {
        if (!image.getAttribute('data-id')) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'relative my-8 not-prose';

        const newImage = image.cloneNode(true);
        newImage.className = 'max-w-full h-auto rounded-lg';

        const downloadLink = document.createElement('a');
        downloadLink.className =
            'image-download-btn absolute top-2 right-2 bg-black text-white rounded-md p-2 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-black inline-flex items-center justify-center';
        downloadLink.href = '#';
        downloadLink.title = 'Download image';
        downloadLink.setAttribute('aria-label', 'Download image');
        downloadLink.setAttribute('data-media-uuid', image.getAttribute('data-id'));
        downloadLink.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                <path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" />
                <path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" />
            </svg>
        `;

        wrapper.appendChild(newImage);
        wrapper.appendChild(downloadLink);
        image.parentElement.replaceChild(wrapper, image);
    });

    return tempDiv.innerHTML;
};

document.addEventListener('alpine:init', () => {
    Alpine.data(
        'chat',
        ({
            csrfToken,
            retryMessageUrl,
            sendMessageUrl,
            completeResponseUrl,
            showThreadUrl,
            downloadImageUrl,
            userId,
            threadId,
        }) => ({
            error: null,
            isIncomplete: false,
            isLoading: true,
            isSendingMessage: false,
            isRateLimited: false,
            isRetryable: true,
            hasImageGeneration: false,
            hasImagePlaceholder: false,
            latestMessage: '',
            message: '',
            rawIncomingResponse: '',
            pendingResponse: '',
            latestPrompt: null,
            messages: [],
            users: [],
            hasSetUpNewMessageForResponse: false,
            isCompletingPreviousResponse: false,
            responseTimeout: null,
            downloadImageUrl,
            csrfToken,
            clickHandler: null,

            init: async function () {
                this.clickHandler = async (event) => {
                    const downloadBtn = event.target.closest('.image-download-btn');
                    if (!downloadBtn) return;

                    event.preventDefault();
                    const mediaUuid = downloadBtn.getAttribute('data-media-uuid');
                    if (!mediaUuid) return;

                    try {
                        const response = await fetch(this.downloadImageUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                media_uuid: mediaUuid,
                            }),
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error || 'Download failed');
                        }

                        const contentDisposition = response.headers.get('content-disposition');
                        let filename = 'image.png';
                        if (contentDisposition) {
                            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }

                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);

                        const tempLink = document.createElement('a');
                        tempLink.href = url;
                        tempLink.download = filename;
                        tempLink.style.display = 'none';
                        document.body.appendChild(tempLink);
                        tempLink.click();
                        document.body.removeChild(tempLink);
                        window.URL.revokeObjectURL(url);
                    } catch (error) {}
                };

                document.addEventListener('click', this.clickHandler);

                this.render();

                setInterval(this.render.bind(this), 500);

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
                            const parsedMarkdown = marked.parse(this.rawIncomingResponse);
                            const htmlWithDownloadButtons = addImageDownloadButtons(parsedMarkdown);
                            this.messages[this.messages.length - 1].content = DOMPurify.sanitize(
                                htmlWithDownloadButtons,
                                {
                                    ADD_TAGS: ['a'],
                                    ADD_ATTR: [
                                        'href',
                                        'aria-label',
                                        'title',
                                        'data-media-uuid',
                                        'data-id',
                                        'class',
                                        'src',
                                        'alt',
                                    ],
                                },
                            );
                        }
                    }
                }, 50);

                const showThreadResponse = await fetch(showThreadUrl, {
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const thread = await showThreadResponse.json();
                this.messages = thread.messages.map((message) => {
                    if (message.content && !message.user_id) {
                        const parsedMarkdown = marked.parse(message.content);
                        const htmlWithDownloadButtons = addImageDownloadButtons(parsedMarkdown);
                        message.content = DOMPurify.sanitize(htmlWithDownloadButtons, {
                            ADD_TAGS: ['a'],
                            ADD_ATTR: [
                                'href',
                                'aria-label',
                                'title',
                                'data-media-uuid',
                                'data-id',
                                'class',
                                'src',
                                'alt',
                            ],
                        });
                    }

                    return message;
                });
                this.users = thread.users;

                Echo.private(`advisor-thread-${threadId}`)
                    .listen('.advisor-message.chunk', (event) => {
                        this.error = null;
                        this.isRateLimited = false;
                        this.hasImagePlaceholder = false;

                        this.startResponseTimeout();

                        if (!this.hasSetUpNewMessageForResponse) {
                            if (!this.isCompletingPreviousResponse) {
                                this.messages.push({
                                    content: '',
                                });

                                this.rawIncomingResponse = '';
                                this.pendingResponse = '';
                            } else {
                                if (this.rawIncomingResponse.endsWith('...')) {
                                    this.rawIncomingResponse = this.rawIncomingResponse.slice(0, -3);
                                }

                                this.rawIncomingResponse += ' ';
                            }

                            this.hasSetUpNewMessageForResponse = true;
                        }

                        this.pendingResponse += event.content;
                    })
                    .listen('.advisor-message.finished', (event) => {
                        this.clearResponseTimeout();
                        this.hasImagePlaceholder = false;

                        if (this.pendingResponse) {
                            this.rawIncomingResponse += this.pendingResponse;
                            this.pendingResponse = '';

                            if (this.messages.length > 0) {
                                const parsedMarkdown = marked.parse(this.rawIncomingResponse);
                                const htmlWithDownloadButtons = addImageDownloadButtons(parsedMarkdown);
                                this.messages[this.messages.length - 1].content = DOMPurify.sanitize(
                                    htmlWithDownloadButtons,
                                    {
                                        ADD_TAGS: ['a'],
                                        ADD_ATTR: [
                                            'href',
                                            'aria-label',
                                            'title',
                                            'data-media-uuid',
                                            'data-id',
                                            'class',
                                            'src',
                                            'alt',
                                        ],
                                    },
                                );
                            }
                        }

                        this.isSendingMessage = !!event.rate_limit_resets_after_seconds;

                        if (event.is_incomplete) {
                            this.isIncomplete = true;

                            return;
                        }

                        if (event.error) {
                            this.error = event.error;
                            this.isRetryable = true;
                            this.isRateLimited = false;

                            return;
                        }

                        if (event.rate_limit_resets_after_seconds) {
                            this.error = 'Heavy traffic, just a few more moments...';
                            this.isRateLimited = true;

                            this.$nextTick(async () => {
                                await new Promise((resolve) =>
                                    setTimeout(resolve, event.rate_limit_resets_after_seconds * 1000),
                                );

                                this.startResponseTimeout();

                                await this.handleResponse({
                                    response: await fetch(
                                        this.isCompletingPreviousResponse ? completeResponseUrl : retryMessageUrl,
                                        {
                                            method: 'POST',
                                            headers: {
                                                Accept: 'application/json',
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken,
                                            },
                                            body: JSON.stringify(
                                                !this.isCompletingPreviousResponse
                                                    ? {
                                                          content: this.latestMessage,
                                                          files: this.$wire.files,
                                                          has_image_generation: this.hasImageGeneration,
                                                      }
                                                    : {},
                                            ),
                                        },
                                    ),
                                });
                            });
                        }

                        this.$wire.refreshThreads();
                    });

                this.isLoading = false;

                this.$watch('isRetryable', async (value) => {
                    while (!this.isRetryable) {
                        // Wait for 10 seconds before checking if the thread is retryable again.
                        await new Promise((resolve) => setTimeout(resolve, 10000));

                        this.isRetryable = !(await this.$wire.isThreadLocked());
                    }
                });
            },

            startResponseTimeout: function () {
                this.clearResponseTimeout();

                this.responseTimeout = setTimeout(
                    () => {
                        this.error = 'An error happened when sending your message.';
                        this.isRetryable = true;
                        this.isRateLimited = false;
                        this.isSendingMessage = false;
                        this.hasImagePlaceholder = false;
                        this.responseTimeout = null;
                    },
                    10 * 60 * 1000,
                );
            },

            clearResponseTimeout: function () {
                if (this.responseTimeout) {
                    clearTimeout(this.responseTimeout);
                    this.responseTimeout = null;
                }
            },

            handleResponse: async function ({ response }) {
                if (!response.ok) {
                    this.clearResponseTimeout();

                    const responseJson = await response.json();

                    this.error = responseJson.message;
                    this.isRetryable = !responseJson.isThreadLocked;
                    this.isRateLimited = false;
                    this.isSendingMessage = false;
                    this.hasImagePlaceholder = false;

                    return;
                }

                this.hasSetUpNewMessageForResponse = false;

                if (this.hasImageGeneration) {
                    this.hasImagePlaceholder = true;
                }

                if (!this.isCompletingPreviousResponse) {
                    this.$wire.clearFiles();
                }
            },

            sendMessage: async function (prompt = null) {
                if (!this.message.replace(/\s/g, '').length && prompt === null) {
                    // The message is empty / whitespace only.

                    return;
                }

                this.isSendingMessage = true;
                this.isIncomplete = false;
                this.error = null;

                this.$dispatch('message-sent', { threadId: threadId });

                const message = this.message;

                if (prompt) {
                    this.latestMessage = '';

                    if (this.messages.slice(-1)[0]?.prompt !== prompt.title) {
                        this.messages.push({
                            prompt: prompt.title,
                        });
                    }
                } else {
                    this.latestMessage = this.message;

                    this.messages.push({
                        content: this.message.replace(/(?:\r\n|\r|\n)/g, '<br />'),
                        user_id: userId,
                    });

                    this.message = '';
                }

                this.$nextTick(async () => {
                    this.isCompletingPreviousResponse = false;

                    this.startResponseTimeout();

                    await this.handleResponse({
                        response: await fetch(sendMessageUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                ...(prompt ? { prompt_id: prompt.id } : { content: message }),
                                files: this.$wire.files,
                                has_image_generation: this.hasImageGeneration,
                            }),
                        }),
                    });
                });
            },

            retryMessage: async function () {
                if (this.latestMessage === '' && this.latestPrompt) {
                    return await this.sendMessage(this.latestPrompt);
                }

                if (this.latestMessage === '') {
                    return;
                }

                const isOriginallyIncomplete = this.isIncomplete;

                this.isSendingMessage = true;
                this.isIncomplete = false;
                this.error = null;

                this.$dispatch('message-sent', { threadId: threadId });

                this.$nextTick(async () => {
                    this.isCompletingPreviousResponse = isOriginallyIncomplete;

                    this.startResponseTimeout();

                    await this.handleResponse({
                        response: await fetch(retryMessageUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                content: this.latestMessage,
                                files: this.$wire.files,
                                has_image_generation: this.hasImageGeneration,
                            }),
                        }),
                    });
                });
            },

            completeResponse: async function () {
                this.isSendingMessage = true;
                this.isIncomplete = false;
                this.error = null;

                this.$dispatch('message-sent', { threadId: threadId });

                this.$nextTick(async () => {
                    this.isCompletingPreviousResponse = true;

                    this.startResponseTimeout();

                    await this.handleResponse({
                        response: await fetch(completeResponseUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
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

            destroy: function () {
                if (this.clickHandler) {
                    document.removeEventListener('click', this.clickHandler);
                    this.clickHandler = null;
                }

                this.clearResponseTimeout();
            },
        }),
    );
});
