document.addEventListener('alpine:init', () => {
    global = globalThis;
    const { Client } = require('@twilio/conversations');

    // Create the Twilio Client
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
        async init() {
            console.log(selectedConversation);
            // TODO: Break out into a separate function
            if (conversationsClient === null) {
                conversationsClient = new Client(await this.$wire.generateToken());

                conversationsClient.on('connectionStateChanged', (state) => {
                    if (state === 'connecting') console.log('Connecting to Twilio…');
                    if (state === 'connected') {
                        console.log('You are connected.');
                    }
                    if (state === 'disconnecting') console.log('Disconnecting from Twilio…');
                    if (state === 'disconnected') console.log('Disconnected from Twilio.');
                    if (state === 'denied') console.log('Failed to connect.');
                });
            }

            if (selectedConversation) {
                this.conversation = await conversationsClient.getConversationBySid(selectedConversation);

                this.conversation.getMessages().then((messages) => {
                    messages.items.forEach(async (message) => {
                        this.messages.push({
                            // TODO: Store these so we don't have to get them per User
                            avatar: await this.$wire.getUserAvatarUrl(message.author),
                            message: message
                        });
                    });
                });

                this.conversation.on('messageAdded', async (message) => {
                    this.messages.push({
                        // TODO: Store these so we don't have to get them per User
                        avatar: await this.$wire.getUserAvatarUrl(message.author),
                        message: message
                    });
                });
            }
            // this.$el.addEventListener('conversationchanged', () => console.log('event fired!'))
        },
        destroy() {
            // runs when the component is removed from the DOM
        },
    }));
});
