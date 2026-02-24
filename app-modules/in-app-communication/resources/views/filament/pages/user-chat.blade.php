{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
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
--}}
@php
    use AdvisingApp\InAppCommunication\Enums\ConversationType;
    use AdvisingApp\InAppCommunication\Models\TwilioConversation;
    use AdvisingApp\InAppCommunication\Models\TwilioConversationUser;
    use Filament\Support\Facades\FilamentAsset;

    $conversationGroups = $this->conversations->reduce(
        function (array $carry, TwilioConversation $conversation): array {
            if ($conversation->type === ConversationType::Channel) {
                $carry[0][] = $conversation;
            } else {
                $carry[1][] = $conversation;
            }
            return $carry;
        },
        [[], []],
    );
@endphp

<x-filament-panels::page full-height="true">
    <div class="flex h-full flex-col">
        <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4">
            <div class="col-span-1">
                <div class="flex flex-col gap-y-6">
                    @foreach ($conversationGroups as $conversations)
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <span class="flex-1 text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                                    {{ $loop->first ? 'Channels' : 'Direct messages' }}
                                </span>

                                @if (count($conversations))
                                    @if ($loop->first)
                                        <div class="flex items-center gap-1">
                                            {{ (clone $this->joinChannelsAction)->iconButton()->size('sm')->tooltip('Join channels')->icon('heroicon-m-list-bullet') }}
                                            {{ (clone $this->newChannelAction)->iconButton()->size('sm')->tooltip('Create a channel')->icon('heroicon-m-plus') }}
                                        </div>
                                    @else
                                        {{ (clone $this->newUserToUserChatAction)->iconButton()->size('sm')->tooltip('Start a chat')->icon('heroicon-m-plus') }}
                                    @endif
                                @endif
                            </div>

                            @if (count($conversations))
                                <ul
                                    class="border-gray-950/5 flex flex-col gap-y-1 rounded-xl border bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"
                                >
                                    @foreach ($conversations as $conversationItem)
                                        @php
                                            /** @var TwilioConversation $conversationItem */
                                        @endphp

                                        <li
                                            @class([
                                                'px-2 group cursor-pointer flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                                'bg-gray-100 dark:bg-white/5' => $conversation?->getKey() === $conversationItem->getKey(),
                                            ])
                                        >
                                            <button
                                                type="button"
                                                @class([
                                                    'relative flex flex-1 items-center justify-between text-start gap-x-3 rounded-lg py-2 text-sm',
                                                ])
                                                wire:click="selectConversation('{{ $conversationItem->getKey() }}')"
                                            >
                                                <span
                                                    @class([
                                                        'flex-1 truncate',
                                                        'text-gray-700 dark:text-gray-200' => $conversation?->getKey() !== $conversationItem->getKey(),
                                                        'text-primary-600 dark:text-primary-400' => $conversation?->getKey() === $conversationItem->getKey(),
                                                    ])
                                                >
                                                    {{ $conversationItem->getLabel() }}
                                                </span>
                                                <div class="flex items-center gap-1">
                                                    @if ($conversationItem->participant->unread_messages_count)
                                                        <x-filament::badge color="warning">
                                                            {{ $conversationItem->participant->unread_messages_count }}
                                                        </x-filament::badge>
                                                    @endif

                                                    @if (! $conversationItem->participant->last_read_at)
                                                        <x-filament::badge color="warning">New</x-filament::badge>
                                                    @endif

                                                    <x-filament::loading-indicator
                                                        :attributes="new \Illuminate\View\ComponentAttributeBag([
                                                            'wire:loading.delay.' .
                                                            config('filament.livewire_loading_delay', 'default') => '',
                                                            'wire:target' =>
                                                                'selectConversation(\'' .
                                                                $conversationItem->getKey() .
                                                                '\')',
                                                        ])->class(['w-5 h-5'])"
                                                    />
                                                </div>
                                            </button>
                                            @php
                                                /** @var TwilioConversationUser $participant */
                                                $participant = $conversationItem->participant;
                                            @endphp

                                            @if ($participant->is_pinned)
                                                {{ ($this->togglePinChannelAction)(['id' => $conversationItem->getKey()])->icon('heroicon-s-star')->tooltip('Unpin') }}
                                            @else
                                                {{ ($this->togglePinChannelAction)(['id' => $conversationItem->getKey()])->icon('heroicon-o-star')->tooltip('Pin') }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif ($loop->first)
                                <div class="text-sm">
                                    You do not belong to any channels yet. You can
                                    {{ (clone $this->joinChannelsAction)->link()->label('browse a list')->tooltip(null)->icon(null) }}
                                    or
                                    {{ (clone $this->newChannelAction)->link()->label('create a new one')->tooltip(null)->icon(null) }}
                                    .
                                </div>
                            @else
                                <div class="text-sm">
                                    You do not have any direct messages yet. You can
                                    {{ (clone $this->newUserToUserChatAction)->link()->label('start one')->tooltip(null)->icon(null) }}
                                    .
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @php
                /** @var TwilioConversation $conversation */
            @endphp

            @if ($conversation)
                <div
                    class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                    x-data="userToUserChat({
                                selectedConversation: @js($conversation->getKey()),
                                users: @js($users),
                                activeUsers: $wire.$entangle('conversationActiveUsers'),
                            })"
                    wire:key="conversation-{{ $conversation->getKey() }}"
                    wire:poll.60s="loadConversationActiveUsers"
                >
                    <div class="flex flex-col items-center self-center" x-show="loading" x-transition.delay.800ms>
                        <x-filament::loading-indicator class="text-primary-500 h-12 w-12" />
                        <p class="text-center" x-text="loadingMessage"></p>
                    </div>
                    <template x-if="!loading && error">
                        <div class="flex flex-col items-center self-center">
                            <x-filament::icon
                                class="text-primary-500 h-12 w-12"
                                icon="heroicon-m-exclamation-triangle"
                            />
                            <p class="text-center">Something went wrong...</p>
                            <p class="text-center" x-text="errorMessage"></p>
                            <x-filament::button class="mt-2" x-on:click="errorRetry">Retry</x-filament::button>
                        </div>
                    </template>
                    <div
                        class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                        x-show="!loading && !error"
                        x-transition.delay.850ms
                    >
                        <div
                            class="border-gray-950/5 flex max-h-[calc(100vh-24rem)] flex-1 flex-col-reverse overflow-y-scroll rounded-xl border text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
                        >
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="message in messages" :key="message.message.index">
                                    <div class="group w-full dark:bg-gray-800">
                                        <div class="m-auto justify-center px-6 py-3 text-base">
                                            <div
                                                class="mx-auto flex flex-1 gap-6 text-base md:max-w-2xl lg:max-w-[38rem] xl:max-w-3xl"
                                            >
                                                <div class="relative mt-1 flex flex-shrink-0 flex-col items-end">
                                                    <div class="relative">
                                                        <x-filament::avatar
                                                            class="rounded-full"
                                                            alt="User Avatar"
                                                            x-bind:src="message.avatar"
                                                            size="lg"
                                                        />

                                                        <div
                                                            class="absolute bottom-0 end-0 h-3 w-3 rounded-full"
                                                            x-bind:class="{
                                                                'bg-success-500': activeUsers.includes(message.authorId),
                                                                'bg-gray-500': ! activeUsers.includes(message.authorId),
                                                            }"
                                                        ></div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="relative flex w-[calc(100%-50px)] flex-col lg:w-[calc(100%-115px)]"
                                                >
                                                    <div class="flex max-w-full flex-grow flex-col gap-y-1">
                                                        <div class="flex items-center gap-2 text-sm">
                                                            <span
                                                                class="font-medium text-gray-700 dark:text-gray-300"
                                                                x-text="message.author"
                                                            ></span>
                                                            <span
                                                                class="text-gray-500 dark:text-gray-500"
                                                                x-text="formatDate(message.date)"
                                                            ></span>
                                                        </div>

                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words"
                                                        >
                                                            <div
                                                                x-html="generateHTML(JSON.parse(message.message.body))"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <template x-if="messagePaginator?.hasPrevPage">
                                <div
                                    class="text-primary-500 mb-auto flex cursor-pointer justify-center bg-white p-3 text-center dark:bg-gray-700"
                                    x-on:click="loadPreviousMessages"
                                >
                                    <p>Load previous messages...</p>
                                    <x-filament::loading-indicator
                                        class="text-primary-500 ml-2 h-4 w-4"
                                        x-show="loadingPreviousMessages"
                                    />
                                </div>
                            </template>
                        </div>

                        <form @submit.prevent="submit">
                            <div
                                class="border-gray-950/5 w-full overflow-hidden rounded-xl border bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700"
                            >
                                <div class="bg-white dark:bg-gray-800">
                                    <div
                                        x-data="chatEditor({ currentUser: @js(auth()->id()), users: @js($users) })"
                                        x-model="message"
                                        x-on:click.outside="
                                            $refs.colorPicker.close()
                                            $refs.emojiPicker.close()
                                        "
                                        wire:ignore
                                        x-modelable="content"
                                    >
                                        <template x-if="isLoaded()">
                                            <div
                                                class="flex flex-wrap items-center gap-1 border-b px-3 py-2 dark:border-gray-700"
                                            >
                                                <button
                                                    class="rounded p-0.5"
                                                    type="button"
                                                    x-on:click="toggleBold()"
                                                    x-bind:class="{ 'bg-gray-200 dark:bg-gray-700': isActive('bold', updatedAt) }"
                                                >
                                                    @svg('icon-bold', 'h-5 w-5')
                                                </button>

                                                <button
                                                    class="rounded p-0.5"
                                                    type="button"
                                                    x-on:click="toggleItalic()"
                                                    x-bind:class="{
                                                        'bg-gray-200 dark:bg-gray-700': isActive('italic', updatedAt),
                                                    }"
                                                >
                                                    @svg('icon-italic', 'h-5 w-5')
                                                </button>

                                                <button
                                                    class="rounded p-0.5"
                                                    type="button"
                                                    x-on:click="toggleUnderline()"
                                                    x-bind:class="{
                                                        'bg-gray-200 dark:bg-gray-700': isActive('underline', updatedAt),
                                                    }"
                                                >
                                                    @svg('icon-underline', 'h-5 w-5')
                                                </button>

                                                <div>
                                                    <button
                                                        class="rounded p-0.5"
                                                        type="button"
                                                        x-on:click="toggleLink"
                                                        x-bind:class="{ 'bg-gray-200 dark:bg-gray-700': isActive('link', updatedAt) }"
                                                    >
                                                        @svg('icon-link', 'mt-0.5 h-4 w-4')
                                                    </button>

                                                    <form
                                                        class="ring-gray-950/5 absolute z-10 w-screen max-w-sm space-y-3 divide-y divide-gray-100 rounded-lg bg-white px-4 py-3 shadow-lg ring-1 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10"
                                                        x-on:submit.prevent="saveLink"
                                                        x-cloak
                                                        x-float.offset.placement.bottom-start="{ offset: 8 }"
                                                        x-ref="linkEditor"
                                                        x-on:click.outside="$el.close"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:leave-end="opacity-0"
                                                    >
                                                        <label class="grid gap-y-2">
                                                            <span
                                                                class="text-sm font-medium leading-6 text-gray-950 dark:text-white"
                                                            >
                                                                URL
                                                            </span>

                                                            <x-filament::input.wrapper>
                                                                <x-filament::input
                                                                    type="url"
                                                                    x-model="linkUrl"
                                                                    x-ref="linkInput"
                                                                />
                                                            </x-filament::input.wrapper>
                                                        </label>

                                                        <div class="flex flex-wrap items-center gap-3">
                                                            <x-filament::button type="submit" size="sm">
                                                                Save
                                                            </x-filament::button>

                                                            <x-filament::button
                                                                type="button"
                                                                size="sm"
                                                                x-show="linkUrl"
                                                                x-on:click="removeLink"
                                                                color="gray"
                                                            >
                                                                Remove
                                                            </x-filament::button>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div>
                                                    <button
                                                        class="rounded p-0.5"
                                                        type="button"
                                                        x-on:click="$refs.colorPicker.toggle"
                                                        x-bind:class="{
                                                            'bg-gray-200 dark:bg-gray-700': isActive('textStyle', updatedAt),
                                                        }"
                                                    >
                                                        @svg('heroicon-c-swatch', 'mt-0.5 h-4 w-4')
                                                    </button>

                                                    <div
                                                        class="ring-gray-950/5 absolute z-10 max-w-xs divide-y divide-gray-100 rounded-lg bg-white px-4 py-3 shadow-lg ring-1 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10"
                                                        x-cloak
                                                        x-float.offset.placement.top-start="{ offset: 8 }"
                                                        x-ref="colorPicker"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:leave-end="opacity-0"
                                                    >
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <button
                                                                class="flex h-5 w-5 items-center rounded-full border bg-gray-50 dark:border-gray-400 dark:bg-gray-800"
                                                                type="button"
                                                                x-on:click="removeColor()"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        ! isActive('textStyle', updatedAt),
                                                                }"
                                                            >
                                                                <span class="sr-only">None</span>

                                                                <div
                                                                    class="flex-1 rotate-45 border-t dark:border-gray-400"
                                                                ></div>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#ef4444]"
                                                                type="button"
                                                                x-on:click="setColor('#ef4444')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#ef4444' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Red</span>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#ec4899]"
                                                                type="button"
                                                                x-on:click="setColor('#ec4899')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#ec4899' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Pink</span>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#3b82f6]"
                                                                type="button"
                                                                x-on:click="setColor('#3b82f6')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#3b82f6' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Blue</span>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#22c55e]"
                                                                type="button"
                                                                x-on:click="setColor('#22c55e')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#22c55e' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Green</span>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#eab308]"
                                                                type="button"
                                                                x-on:click="setColor('#eab308')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#eab308' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Yellow</span>
                                                            </button>

                                                            <button
                                                                class="h-5 w-5 rounded-full bg-[#737373]"
                                                                type="button"
                                                                x-on:click="setColor('#737373')"
                                                                x-bind:class="{
                                                                    'ring-2 ring-offset-2 ring-primary-600 dark:ring-offset-gray-900':
                                                                        updatedAt && isActive('textStyle', { color: '#737373' }),
                                                                }"
                                                            >
                                                                <span class="sr-only">Gray</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <button
                                                        class="rounded p-0.5"
                                                        type="button"
                                                        x-on:click="$refs.emojiPicker.toggle"
                                                    >
                                                        @svg('heroicon-c-face-smile', 'mt-0.5 h-4 w-4')
                                                    </button>

                                                    <div
                                                        class="ring-gray-950/5 absolute z-10 max-w-xs divide-y divide-gray-100 rounded-lg bg-white px-4 py-3 shadow-lg ring-1 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10"
                                                        x-cloak
                                                        x-float.offset.placement.top-start="{ offset: 8 }"
                                                        x-ref="emojiPicker"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:leave-end="opacity-0"
                                                    >
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <template
                                                                x-for="emoji in ['😀', '😂', '👍', '👎', '🙏', '😕', '🤔', '😊', '🎉', '💼', '🕒', '📅', '🔒', '❗', '❓', '💡', '🚫', '✅', '🤖', '📧', '🌐', '💬', '📈', '📉', '🤝']"
                                                            >
                                                                <button
                                                                    class="h-5 w-5 rounded hover:bg-gray-100 dark:hover:bg-gray-800"
                                                                    type="button"
                                                                    x-on:click="insertContent(emoji)"
                                                                    x-text="emoji"
                                                                    x-bind:key="emoji"
                                                                ></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="w-full px-4 py-2 text-gray-900 dark:text-white">
                                            <div x-ref="element"></div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-gray-200 px-3 py-2 dark:border-gray-600"
                                >
                                    <div class="flex items-center gap-3">
                                        <x-filament::button type="submit">Send</x-filament::button>

                                        <div
                                            class="relative flex h-6 items-center justify-center gap-0.5"
                                            x-show="usersTyping.length"
                                        >
                                            <template x-for="user in usersTyping">
                                                <x-filament::avatar
                                                    alt="User Avatar"
                                                    size="w-4 h-4"
                                                    x-bind:src="user.avatar"
                                                />
                                            </template>
                                            <span
                                                class="bg-primary-500 text-primary-500 h-2 w-2 animate-bounce rounded-full animation-delay-100"
                                            ></span>
                                            <span
                                                class="bg-primary-500 text-primary-500 h-2 w-2 animate-bounce rounded-full animation-delay-200"
                                            ></span>
                                            <span
                                                class="bg-primary-500 text-primary-500 h-2 w-2 animate-bounce rounded-full animation-delay-300"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @php
                            $isManager = (bool) $conversation->managers()->find(auth()->user());
                        @endphp

                        <div class="{{ $isManager ? 'justify-between' : 'justify-end' }} flex items-center">
                            @if ($conversation->type === ConversationType::Channel)
                                @if ($isManager)
                                    <div class="flex gap-3">
                                        {{ $this->editChannelAction }}

                                        {{ $this->deleteChannelAction }}
                                    </div>
                                @endif
                            @endif

                            <div class="flex gap-3">
                                {{ $this->updateNotificationPreferenceAction }}

                                @if ($conversation->type === ConversationType::Channel)
                                    {{ $this->addUserToChannelAction }}

                                    {{ $this->leaveChannelAction }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3">
                    <p class="text-center text-xl">Select or create a new Chat</p>
                </div>
            @endif
        </div>

        @vite('app-modules/in-app-communication/resources/js/userToUserChat.js')

        <style>
            .tiptap .is-editor-empty:first-child::before {
                color: #adb5bd;
                content: attr(data-placeholder);
                float: left;
                height: 0;
                pointer-events: none;
            }

            .ProseMirror-focused {
                outline-color: transparent;
            }

            span[data-type='mention'][data-id='{{ auth()->id() }}'] {
                background-color: #fcd34d55;
                border-radius: 3px;
            }
        </style>
    </div>

    <x-filament::modal id="confirmSafeLink" width="2xl">
        <x-slot name="heading">
            <h3 class="text-3xl">Link confirmation</h3>
        </x-slot>

        <x-slot name="description">
            <div
                class="mt-4 flex flex-col"
                x-data="{ href: null }"
                x-on:open-modal.window="href = $event.detail.href"
            >
                You are about to open another browser tab and visit:
                <strong class="mt-2" x-text="href"></strong>
            </div>
        </x-slot>

        <x-slot name="footerActions">
            <div
                class="flex w-full justify-between"
                x-data="{ href: null }"
                x-on:open-modal.window="href = $event.detail.href"
            >
                <x-filament::button x-on:click="close()" color="gray" outlined>Cancel</x-filament::button>
                <a :href="href" target="_blank">
                    <x-filament::button color="primary" x-on:click="close()">Continue</x-filament::button>
                </a>
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>
