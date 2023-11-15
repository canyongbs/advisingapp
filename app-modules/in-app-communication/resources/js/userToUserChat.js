document.addEventListener('alpine:init', () => {
    global = globalThis;
    const { Client } = require('@twilio/conversations');

    let avatarCache = {};

    let conversationsClient = null;

    Alpine.data('userToUserChat', (selectedConversation) => ({
        conversation: null,
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
                // TODO: Add a spinner or something to indicate that the client is connecting.
                if (state === 'connecting') console.log('Connecting to Twilio…');
                if (state === 'connected') {
                    console.log('You are connected.');
                }
                if (state === 'disconnecting') console.log('Disconnecting from Twilio…');
                if (state === 'disconnected') console.log('Disconnected from Twilio.');
                if (state === 'denied') console.log('Failed to connect.');
            });

            conversationsClient.on("tokenAboutToExpire", async () => {
                conversationsClient.updateToken(await this.$wire.generateToken()).catch((error) => this.handleError(error));
            });

            conversationsClient.on("tokenExpired", async () => {
                conversationsClient.updateToken(await this.$wire.generateToken()).catch((error) => this.handleError(error));
            });

            return conversationsClient;
        },
        async init() {
            if (conversationsClient === null) {
                conversationsClient = this.initializeClient();
            }

            if (selectedConversation) {
                this.conversation = await conversationsClient.getConversationBySid(selectedConversation).catch((error) => this.handleError(error));

                this.conversation.getMessages().then((messages) => {
                    messages.items.forEach(async (message) => {
                        this.messages.push({
                            avatar: this.getAvatarUrl(message.author),
                            message: message
                        });
                    });

                    this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
                })
                  .catch((error) => this.handleError(error));

                this.conversation.on('messageAdded', async (message) => {
                    this.messages.push({
                        avatar: this.getAvatarUrl(message.author),
                        message: message
                    });

                    this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));
                });
            }
        },
        handleError(error) {
            console.error('Chat client error occurred, sending to error handler…');

            this.$wire.handleError(JSON.stringify(error, Object.getOwnPropertyNames(error))).then(() => console.info('Chat client error sent to error handler.')).catch((error) => console.error('Error handler failed to handle error: ', error));
        }
    }));
});
