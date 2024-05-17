/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
document.addEventListener('alpine:init', () => {
    Alpine.data('chat', ({ csrfToken, retryMessageUrl, sendMessageUrl, showThreadUrl, userId }) => ({
        isError: false,
        isLoading: true,
        isSendingMessage: false,
        latestMessage: '',
        message: '',
        messages: [],
        users: [],

        init: async function () {
            this.render();

            setInterval(this.render.bind(this), 500);

            const showThreadResponse = await fetch(showThreadUrl, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            const thread = await showThreadResponse.json();
            this.messages = thread.messages;
            this.users = thread.users;

            this.isLoading = false;
        },

        sendMessage: async function () {
            if (!this.message.replace(/\s/g, '').length) {
                // The message is empty / whitespace only.

                return;
            }

            this.isSendingMessage = true;
            this.isError = false;

            this.latestMessage = this.message;

            this.messages.push({
                content: this.message.replace(/(?:\r\n|\r|\n)/g, '<br />'),
                user_id: userId,
            });

            const message = this.message;

            this.message = '';

            this.$nextTick(async () => {
                const sendMessageResponse = await fetch(sendMessageUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ content: message }),
                });

                const response = await sendMessageResponse.json();

                if (!sendMessageResponse.ok) {
                    this.isError = true;
                    this.isSendingMessage = false;

                    return;
                }

                this.messages.push({
                    content: response.content,
                });

                this.isSendingMessage = false;
            });
        },

        retryMessage: async function () {
            this.isSendingMessage = true;
            this.isError = false;

            this.$nextTick(async () => {
                const retryMessageResponse = await fetch(retryMessageUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ content: this.latestMessage }),
                });

                const response = await retryMessageResponse.json();

                if (!retryMessageResponse.ok) {
                    this.isError = true;
                    this.isSendingMessage = false;

                    return;
                }

                this.messages.push({
                    content: response.content,
                });

                this.isSendingMessage = false;
            });
        },

        render: function () {
            this.$refs.chatContainer.style.maxHeight = 'calc(100dvh - 20rem)';

            if (this.$refs.messageInput.scrollHeight > 0) {
                this.$refs.messageInput.style.height = '5rem';
                this.$refs.messageInput.style.height = `min(${this.$refs.messageInput.scrollHeight}px, 35dvh)`;

                this.$refs.chatContainer.style.maxHeight = `calc(100dvh - 15rem - ${this.$refs.messageInput.style.height})`;
            }
        },
    }));
});
