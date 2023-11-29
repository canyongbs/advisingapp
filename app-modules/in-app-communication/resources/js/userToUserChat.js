/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
    global = globalThis;
    const { Client } = require('@twilio/conversations');

    let avatarCache = {};

    let conversationsClient = null;

    Alpine.data('userToUserChat', (selectedConversation) => ({
        loading: true,
        loadingMessage: 'Loading chat…',
        error: false,
        errorMessage: '',
        conversation: null,
        messagePaginator: null,
        loadingPreviousMessages: false,
        messages: [],
        message: '',
        usersTyping: [],
        submit: function () {
            if (this.message.length === 0 || this.conversation === null) return;

            this.conversation.sendMessage(this.message).catch((error) => this.handleError(error));

            this.message = '';
        },
        async getAvatarUrl(userId) {
            if (avatarCache[userId]) return avatarCache[userId];

            avatarCache[userId] = await this.$wire.getUserAvatarUrl(userId);

            return avatarCache[userId];
        },
        async initializeClient() {
            conversationsClient = new Client(await this.$wire.generateToken());

            conversationsClient.on('connectionStateChanged', (state) => {
                switch (state) {
                    case 'connecting':
                        this.loading = true;
                        this.loadingMessage = 'Connecting to chat…';
                        this.error = false;
                        this.errorMessage = '';
                        break;
                    case 'connected':
                        this.loading = false;
                        this.loadingMessage = 'Connected to chat.';
                        break;
                    case 'disconnecting':
                        this.loading = true;
                        this.loadingMessage = 'Disconnecting from chat…';
                        this.error = false;
                        this.errorMessage = '';
                        break;
                    case 'disconnected':
                        this.loading = false;
                        this.loadingMessage = 'Disconnected from chat.';
                        this.error = false;
                        this.errorMessage = '';
                        break;
                    case 'denied':
                        this.loading = false;
                        this.loadingMessage = 'Failed to connect.';
                        this.error = true;
                        this.errorMessage = 'Failed to connect to chat. Please try again later.';
                        break;
                    default:
                        console.log('Unknown connection state: ', state);
                        break;
                }
            });

            conversationsClient.on('tokenAboutToExpire', async () => {
                await this.attemptReconnect();
            });

            conversationsClient.on('tokenExpired', async () => {
                await this.attemptReconnect();
            });

            return conversationsClient;
        },
        async attemptReconnect() {
            conversationsClient
                .updateToken(await this.$wire.generateToken(true))
                .catch((error) => this.handleError(error));
        },
        async init() {
            if (conversationsClient === null) {
                conversationsClient = await this.initializeClient();
            }

            if (selectedConversation) {
                this.loadingMessage = 'Loading conversation…';

                this.conversation = await conversationsClient
                    .getConversationBySid(selectedConversation)
                    .catch((error) => {
                        this.error = true;
                        this.handleError(error);
                    });

                await this.getMessages();

                this.conversation.on('messageAdded', async (message) => {
                    this.messages.push({
                        avatar: await this.getAvatarUrl(message.author),
                        message: message,
                    });

                    this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
                });

                this.conversation.on('messageUpdated', async (data) => {
                    const index = this.messages.findIndex((localMessage) => {
                        return localMessage.message.sid === data.message.sid;
                    });

                    if (index !== -1) {
                        this.messages[index] = {
                            avatar: await this.getAvatarUrl(data.message.author),
                            message: data.message,
                        };
                    }
                });

                this.conversation.on('typingStarted', async (participant) => {
                    const index = this.usersTyping.findIndex((user) => {
                        return participant.identity === participant.identity;
                    });

                    if (index === -1) {
                        this.usersTyping.push({
                            identity: participant.identity,
                            avatar: await this.getAvatarUrl(participant.identity),
                        });
                    }
                });

                this.conversation.on('typingEnded', (participant) => {
                    const index = this.usersTyping.findIndex((user) => {
                        return participant.identity === participant.identity;
                    });

                    if (index !== -1) {
                        this.usersTyping.splice(index, 1);
                    }
                });

                this.loading = false;
            }
        },
        async getMessages() {
            this.loadingMessage = 'Loading messages…';

            this.conversation
                .getMessages()
                .then((messages) => {
                    this.messagePaginator = messages;

                    messages.items.forEach(async (message) => {
                        this.messages.push({
                            avatar: await this.getAvatarUrl(message.author),
                            message: message,
                        });
                    });

                    this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
                })
                .catch((error) => {
                    this.error = true;
                    this.handleError(error);
                });

            this.loadingMessage = 'Messages loaded...';
        },
        async loadPreviousMessages() {
            if (this.messagePaginator?.hasPrevPage) {
                this.loadingPreviousMessages = true;

                this.messagePaginator
                    .prevPage()
                    .then((messages) => {
                        this.messagePaginator = messages;

                        messages.items.forEach(async (message) => {
                            this.messages.unshift({
                                avatar: await this.getAvatarUrl(message.author),
                                message: message,
                            });
                        });
                    })
                    .catch((error) => this.handleError(error));

                this.loadingPreviousMessages = false;
            }
        },
        async errorRetry() {
            this.error = false;
            this.errorMessage = '';
            this.loading = true;

            if (conversationsClient.connectionState === 'connected') {
                await this.getMessages();
                this.loading = false;
                return;
            }

            await this.initializeClient();
        },
        handleError(error) {
            console.error('Chat client error occurred, sending to error handler…');

            this.$wire
                .handleError(JSON.stringify(error, Object.getOwnPropertyNames(error)))
                .then(() => console.info('Chat client error sent to error handler.'))
                .catch((error) => console.error('Error handler failed to handle error: ', error));
        },
        typing(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                this.submit();
            } else {
                this.conversation?.typing();
            }
        },
    }));
});
