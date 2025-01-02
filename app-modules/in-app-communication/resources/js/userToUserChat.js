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
document.addEventListener('alpine:init', () => {
    global = globalThis;
    const { generateHTML } = require('@tiptap/html');
    const { Color } = require('@tiptap/extension-color');
    const { Editor } = require('@tiptap/core');
    const { SafeLink } = require('./TipTap/Extentions/SafeLink');
    const { Mention } = require('./TipTap/Extentions/Mention');
    const { Placeholder } = require('@tiptap/extension-placeholder');
    const { StarterKit } = require('@tiptap/starter-kit');
    const { TextStyle } = require('@tiptap/extension-text-style');
    const { Underline } = require('@tiptap/extension-underline');
    const { Client } = require('@twilio/conversations');

    let avatarCache = {};

    let authorCache = {};

    let conversationsClient = null;

    Alpine.data('userToUserChat', ({ selectedConversation, users, activeUsers }) => ({
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
        activeUsers,
        submit: function () {
            if (this.conversation === null) return;

            let messageContent = [JSON.parse(this.message)];

            let i = 0;
            let messageHasContent = false;

            while (i < messageContent.length) {
                if (['text', 'mention'].includes(messageContent[i].type)) {
                    messageHasContent = true;

                    break;
                }

                (messageContent[i].content ?? []).forEach((content) => messageContent.push(content));

                i++;
            }

            if (!messageHasContent) {
                return;
            }

            this.conversation.sendMessage(this.message).catch((error) => this.handleError(error));

            window.dispatchEvent(new CustomEvent('clearChatMessage'));
        },
        formatDate: (date) => {
            if (date.toDateString() === new Date().toDateString()) {
                return `Today at ${new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
            }

            return `${new Date(date).toLocaleDateString([], { month: 'short', day: 'numeric' })} at ${new Date(
                date,
            ).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
        },
        async getAvatarUrl(userId) {
            if (avatarCache[userId]) return avatarCache[userId];

            avatarCache[userId] = await this.$wire.getUserAvatarUrl(userId);

            return avatarCache[userId];
        },
        async getAuthorName(userId) {
            if (authorCache[userId]) return authorCache[userId];

            authorCache[userId] = await this.$wire.getUserName(userId);

            return authorCache[userId];
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
                        author: await this.getAuthorName(message.author),
                        authorId: message.author,
                        date: message.dateCreated,
                        message: message,
                    });

                    this.conversation.setAllMessagesRead().catch((error) => this.handleError(error));

                    await this.$wire.onMessageSent(message.author, message.sid, JSON.parse(message.body));
                });

                this.conversation.on('messageUpdated', async (data) => {
                    const index = this.messages.findIndex((localMessage) => {
                        return localMessage.message.sid === data.message.sid;
                    });

                    if (index !== -1) {
                        this.messages[index] = {
                            avatar: await this.getAvatarUrl(data.message.author),
                            author: await this.getAuthorName(data.message.author),
                            authorId: data.message.author,
                            data: data.message.dateCreated,
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

            window.addEventListener('chatTyping', () => {
                this.conversation?.typing();
            });

            window.addEventListener('click', (event) => {
                const target = event.target;

                if (target.matches('[data-safe-link]')) {
                    this.openConfirmationModal(target.getAttribute('href'));
                }
            });
        },
        openConfirmationModal(href) {
            this.$dispatch('open-modal', { id: 'confirmSafeLink', href: href });
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
                            author: await this.getAuthorName(message.author),
                            authorId: message.author,
                            date: message.dateCreated,
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
                                author: await this.getAuthorName(message.author),
                                authorId: message.author,
                                date: message.dateCreated,
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
        generateHTML: (content) => {
            return generateHTML(content, [
                Color,
                SafeLink.configure({
                    openOnClick: false,
                    HTMLAttributes: {
                        class: 'underline font-medium text-primary-600 dark:text-primary-500',
                    },
                }),
                Mention.configure({
                    users,
                }),
                StarterKit,
                TextStyle,
                Underline,
            ]);
        },
    }));

    Alpine.data('chatEditor', ({ currentUser, users }) => {
        let editor;

        return {
            content: null,

            updatedAt: Date.now(),

            linkUrl: null,

            init() {
                const _this = this;

                delete users[currentUser];

                editor = new Editor({
                    element: this.$refs.element,
                    extensions: [
                        Color,
                        SafeLink.configure({
                            openOnClick: false,
                            HTMLAttributes: {
                                class: 'underline font-medium text-primary-600 dark:text-primary-500',
                            },
                        }),
                        Mention.configure({
                            users,
                        }),
                        StarterKit,
                        TextStyle,
                        Underline,
                        Placeholder.configure({
                            placeholder: 'Write a message...',
                        }),
                    ],
                    onCreate({ editor }) {
                        _this.updatedAt = Date.now();
                        _this.content = JSON.stringify(editor.getJSON());
                    },
                    onUpdate({ editor }) {
                        _this.updatedAt = Date.now();
                        _this.content = JSON.stringify(editor.getJSON());

                        window.dispatchEvent(new CustomEvent('chatTyping'));
                    },
                    onSelectionUpdate({ editor }) {
                        _this.updatedAt = Date.now();
                        _this.content = JSON.stringify(editor.getJSON());
                    },
                });

                window.addEventListener('clearChatMessage', () => {
                    editor.commands.clearContent(true);
                });
            },
            isLoaded() {
                return editor;
            },
            isActive(type, opts = {}) {
                return editor.isActive(type, opts);
            },
            toggleBold() {
                editor.chain().toggleBold().focus().run();
            },
            toggleItalic() {
                editor.chain().toggleItalic().focus().run();
            },
            toggleLink(event) {
                this.linkUrl = editor.getAttributes('link').href;

                this.$refs.linkEditor.open(event);
                this.$nextTick(() => this.$refs.linkInput.focus());
            },
            toggleUnderline() {
                editor.chain().toggleUnderline().focus().run();
            },
            saveLink(event) {
                if (!this.linkUrl) {
                    this.removeLink();

                    this.$refs.linkEditor.close(event);

                    return;
                }

                editor.chain().focus().extendMarkRange('link').setLink({ href: this.linkUrl }).run();

                this.$refs.linkEditor.close(event);
            },
            removeLink(event) {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();

                this.$refs.linkEditor.close(event);
            },
            setColor(color) {
                editor.chain().focus().setColor(color).run();
            },
            removeColor() {
                editor.chain().focus().unsetColor().run();
            },
            insertContent(content) {
                editor.chain().focus().insertContent(content).run();
            },
        };
    });
});
