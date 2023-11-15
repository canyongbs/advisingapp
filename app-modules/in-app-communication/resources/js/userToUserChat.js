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

                this.conversation.sendMessage(this.message);

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
                conversationsClient.updateToken(await this.$wire.generateToken());
            });

            conversationsClient.on("tokenExpired", async () => {
                conversationsClient.updateToken(await this.$wire.generateToken());
            });

            return conversationsClient;
        },
        async init() {
            if (conversationsClient === null) {
                conversationsClient = this.initializeClient();
            }

            if (selectedConversation) {
                this.conversation = await conversationsClient.getConversationBySid(selectedConversation);

                this.conversation.getMessages().then((messages) => {
                    messages.items.forEach(async (message) => {
                        this.messages.push({
                            avatar: this.getAvatarUrl(message.author),
                            message: message
                        });
                    });
                });

                this.conversation.on('messageAdded', async (message) => {
                    this.messages.push({
                        avatar: this.getAvatarUrl(message.author),
                        message: message
                    });
                });
            }
            // This way of setting up an event listener does not seem to work here.
            // this.$el.addEventListener('conversationchanged', () => console.log('event fired!'))
        },
        destroy() {
            // runs when the component is removed from the DOM
        },
    }));
});
