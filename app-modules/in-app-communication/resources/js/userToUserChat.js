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
        submit: {
            ['@submit.prevent']() {
                if (this.message.length === 0 || this.conversation === null) return;

                this.conversation.sendMessage(this.message).catch((error) => this.handleError(error));

                this.message = '';
            },
        },
        getAvatarUrl: async function (userId) {
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

            conversationsClient.on("tokenAboutToExpire", async () => {
                await this.attemptReconnect();
            });

            conversationsClient.on("tokenExpired", async () => {
                await this.attemptReconnect();
            });

            return conversationsClient;
        },
        async attemptReconnect() {
            conversationsClient.updateToken(await this.$wire.generateToken(true)).catch((error) => this.handleError(error));
        },
        async init() {
            if (conversationsClient === null) {
                conversationsClient = await this.initializeClient();
            }

            if (selectedConversation) {
                await this.getMessages();
            }
        },
        async getMessages() {
            this.loadingMessage = 'Loading conversation…';

            this.conversation = await conversationsClient.getConversationBySid(selectedConversation).catch((error) => {
                this.error = true;
                this.handleError(error);
            });

            this.loadingMessage = 'Loading messages…';

            this.conversation.getMessages().then((messages) => {
                this.messagePaginator = messages;
                console.log(this.messagePaginator);

                messages.items.forEach(async (message) => {
                    this.messages.push({
                        avatar: this.getAvatarUrl(message.author),
                        message: message
                    });
                });

                this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
            })
              .catch((error) => {
                  this.error = true;
                  this.handleError(error)
              });

            this.loadingMessage = 'Messages loaded...';

            this.loading = false;

            this.conversation.on('messageAdded', async (message) => {
                this.messages.push({
                    avatar: this.getAvatarUrl(message.author),
                    message: message
                });

                this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
            });
        },
        async loadPreviousMessages()
        {
            if (this.messagePaginator?.hasPrevPage) {
                this.loadingPreviousMessages = true;

                this.messagePaginator.prevPage().then((messages) => {
                    this.messagePaginator = messages;

                    messages.items.forEach(async (message) => {
                        this.messages.unshift({
                            avatar: this.getAvatarUrl(message.author),
                            message: message
                        });
                    });
                }).catch((error) => this.handleError(error));

                this.loadingPreviousMessages = false;
            }
        },
        async errorRetry() {
            this.error = false;
            this.errorMessage = '';
            this.loading = true;

            if (conversationsClient.connectionState === 'connected') {
                await this.getMessages();
                return;
            }

            await this.initializeClient();
        },
        handleError(error) {
            console.error('Chat client error occurred, sending to error handler…');

            this.$wire.handleError(JSON.stringify(error, Object.getOwnPropertyNames(error))).then(() => console.info('Chat client error sent to error handler.')).catch((error) => console.error('Error handler failed to handle error: ', error));
        }
    }));
});
