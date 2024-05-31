{{--
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
--}}
@php
    use Filament\Support\Enums\ActionSize;
@endphp

<div class="flex h-full flex-col">
    @if ($this->isConsented && $this->thread)
        <div
            class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-3 2xl:grid-cols-4"
            x-data="chats"
        >
            <div class="col-span-1 select-none">
                <div class="flex flex-col gap-y-2">
                    <div
                        class="relative"
                        x-data="{ isSearchingAssistants: false }"
                    >
                        <div class="flex flex-col gap-2">
                            <div class="grid w-full grid-cols-2 gap-2">
                                <x-filament::button
                                    icon="heroicon-m-plus"
                                    wire:click="createThread"
                                >
                                    New chat
                                </x-filament::button>

                                {{ $this->newFolderAction }}
                            </div>

                            @if ($this->customAssistants)
                                <x-filament::button
                                    color="gray"
                                    icon="heroicon-m-magnifying-glass"
                                    x-on:click="isSearchingAssistants = ! isSearchingAssistants"
                                >
                                    New chat with assistant
                                </x-filament::button>
                            @endif
                        </div>

                        @if ($this->customAssistants)
                            <div
                                class="absolute right-0 z-10 mt-2 w-full rounded-lg bg-white p-2 shadow-lg ring-1 ring-gray-950/5 transition dark:bg-gray-900 dark:ring-white/10"
                                x-show="isSearchingAssistants"
                                x-on:click.outside="isSearchingAssistants = false"
                                x-on:close-assistant-search.window="isSearchingAssistants = false"
                            >
                                {{ $this->assistantSwitcherForm }}
                            </div>
                        @endif
                    </div>

                    @if (count($this->threadsWithoutAFolder))
                        <ul
                            class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"
                            id="folder-{{ null }}"
                            x-on:drop.prevent="drop('{{ null }}')"
                            x-on:dragenter.prevent
                            x-on:dragover.prevent
                        >
                            @foreach ($this->threadsWithoutAFolder as $threadItem)
                                <li
                                    id="chat-{{ $threadItem->id }}"
                                    wire:key="chat-{{ $threadItem->id }}"
                                    @class([
                                        'px-2 group flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                        'bg-gray-100 dark:bg-white/5' => $this->thread->is($threadItem),
                                    ])
                                >
                                    <div class="flex flex-1 items-center gap-3">
                                        @if (count($this->folders))
                                            <button
                                                type="button"
                                                draggable="true"
                                                x-on:dragstart="start('{{ $threadItem->id }}', '{{ null }}')"
                                                x-on:dragend="end"
                                                @class([
                                                    'flex items-center cursor-move',
                                                    'text-gray-700 dark:text-gray-200' => !$this->thread->is($threadItem),
                                                    'text-primary-600 dark:text-primary-400' => $this->thread->is($threadItem),
                                                ])
                                            >
                                                <x-heroicon-m-bars-2
                                                    class="h-5 w-5"
                                                    wire:target="selectThread('{{ $threadItem->id }}')"
                                                    wire:loading.remove.delay.none
                                                />

                                                <x-filament::loading-indicator
                                                    class="h-5 w-5"
                                                    wire:target="selectThread('{{ $threadItem->id }}')"
                                                    wire:loading.delay.none
                                                />
                                            </button>
                                        @endif

                                        <button
                                            class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-left text-sm"
                                            type="button"
                                            wire:click="selectThread('{{ $threadItem->id }}')"
                                        >
                                            <span @class([
                                                'flex-1 truncate',
                                                'text-gray-700 dark:text-gray-200' => !$this->thread->is($threadItem),
                                                'text-primary-600 dark:text-primary-400' => $this->thread->is($threadItem),
                                            ])>
                                                {{ $threadItem->name }}
                                            </span>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        {{ ($this->moveThreadAction)(['thread' => $threadItem->id]) }}
                                        {{ ($this->editThreadAction)(['thread' => $threadItem->id]) }}
                                        {{ ($this->deleteThreadAction)(['thread' => $threadItem->id]) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div
                            class="flex flex-col gap-y-1 rounded-xl border border-dashed border-gray-950/5 bg-white px-3 py-2 text-gray-500 shadow-sm dark:border-white/10 dark:bg-gray-900"
                            x-show="dragging"
                            x-on:drop.prevent="drop('{{ null }}')"
                            x-on:dragenter.prevent
                            x-on:dragover.prevent
                        >
                            <div class="text-sm">
                                Drag chats here to move them out of a folder
                            </div>
                        </div>
                    @endif

                    @if (count($this->folders))
                        @foreach ($this->folders as $folder)
                            <ul
                                class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"
                                id="folder-{{ $folder->id }}"
                                x-on:drop.prevent="drop('{{ $folder->id }}')"
                                x-on:dragenter.prevent
                                x-on:dragover.prevent
                            >
                                <span
                                    class="group flex w-full cursor-move items-center rounded-lg px-2 outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5"
                                >
                                    <x-filament::icon-button
                                        icon="heroicon-o-folder-open"
                                        x-show="expanded('{{ $folder->id }}')"
                                        x-on:click="expand('{{ $folder->id }}')"
                                    />
                                    <x-filament::icon-button
                                        icon="heroicon-o-folder"
                                        x-show="expanded('{{ $folder->id }}') === false"
                                        x-on:click="expand('{{ $folder->id }}')"
                                    />

                                    <div
                                        class="group flex w-full cursor-pointer items-center space-x-1 rounded-lg px-2 outline-none transition duration-75 focus:bg-gray-100 dark:focus:bg-white/5">
                                        <div
                                            class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm"
                                            x-on:click="expand('{{ $folder->id }}')"
                                        >
                                            <div class="flex-1 truncate">
                                                @if ($folder->threads->count())
                                                    {{ $folder->name }} ({{ $folder->threads->count() }})
                                                @else
                                                    {{ $folder->name }}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            {{ ($this->renameFolderAction)(['folder' => $folder->id]) }}
                                            {{ ($this->deleteFolderAction)(['folder' => $folder->id]) }}
                                        </div>
                                    </div>
                                </span>
                                @foreach ($folder->threads as $threadItem)
                                    <li
                                        id="chat-{{ $threadItem->id }}"
                                        wire:key="chat-{{ $threadItem->id }}"
                                        x-show="expanded('{{ $folder->id }}')"
                                        @class([
                                            'px-2 group flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                            'bg-gray-100 dark:bg-white/5' => $this->thread->is($threadItem),
                                        ])
                                    >
                                        <div class="flex flex-1 items-center gap-3">
                                            <button
                                                type="button"
                                                draggable="true"
                                                x-on:dragstart="start('{{ $threadItem->id }}', '{{ $folder->id }}')"
                                                x-on:dragend="end"
                                                @class([
                                                    'flex items-center cursor-move',
                                                    'text-gray-700 dark:text-gray-200' => !$this->thread->is($threadItem),
                                                    'text-primary-600 dark:text-primary-400' => $this->thread->is($threadItem),
                                                ])
                                            >
                                                <x-heroicon-m-bars-2
                                                    class="h-5 w-5"
                                                    wire:target="selectThread('{{ $threadItem->id }}')"
                                                    wire:loading.remove.delay.none
                                                />

                                                <x-filament::loading-indicator
                                                    class="h-5 w-5"
                                                    wire:target="selectThread('{{ $threadItem->id }}')"
                                                    wire:loading.delay.none
                                                />
                                            </button>

                                            <button
                                                class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-left text-sm"
                                                type="button"
                                                wire:click="selectThread('{{ $threadItem->id }}')"
                                            >
                                                <span @class([
                                                    'flex-1 truncate',
                                                    'text-gray-700 dark:text-gray-200' => !$this->thread->is($threadItem),
                                                    'text-primary-600 dark:text-primary-400' => $this->thread->is($threadItem),
                                                ])>
                                                    {{ $threadItem->name }}
                                                </span>
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            {{ ($this->moveThreadAction)(['thread' => $threadItem->id]) }}
                                            {{ ($this->editThreadAction)(['thread' => $threadItem->id]) }}
                                            {{ ($this->deleteThreadAction)(['thread' => $threadItem->id]) }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    @endif
                </div>
            </div>

            <div
                class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-2 2xl:col-span-3"
                x-data="chat({
                    csrfToken: @js(csrf_token()),
                    retryMessageUrl: @js(route('ai.threads.messages.retry', ['thread' => $this->thread])),
                    sendMessageUrl: @js(route('ai.threads.messages.send', ['thread' => $this->thread])),
                    showThreadUrl: @js(route('ai.threads.show', ['thread' => $this->thread])),
                    userId: @js(auth()->user()->id),
                })"
                wire:key="thread{{ $this->thread->id }}"
            >
                <div class="flex flex-col items-center justify-between gap-3 md:flex-row">
                    @if ($this->customAssistants)
                        <x-filament::badge :size="ActionSize::Large">
                            <h1 class="text-base">
                                {{ $this->thread->assistant->name }}
                            </h1>
                        </x-filament::badge>
                    @endif

                    @if (!$this->thread->assistant->is_default)
                        <x-filament::link
                            :color="$this->thread->assistant->isUpvoted() ? 'success' : 'gray'"
                            icon="heroicon-m-chevron-up"
                            tag="button"
                            wire:click="toggleAssistantUpvote"
                        >
                            {{ $this->thread->assistant->isUpvoted() ? 'Upvoted' : 'Upvote' }} assistant
                            ({{ $this->thread->assistant->upvotes()->count() }})
                        </x-filament::link>
                    @endif
                </div>

                <div
                    class="flex max-h-[calc(100dvh-20rem)] flex-1 flex-col-reverse overflow-y-scroll rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
                    x-ref="chatContainer"
                >
                    <div
                        class="bg-danger-100 px-4 py-2 dark:bg-danger-900"
                        x-cloak
                        x-show="isError"
                    >
                        An error happened when sending your message,
                        <x-filament::link
                            x-on:click="retryMessage"
                            tag="button"
                            color="gray"
                        >click here to retry.
                        </x-filament::link>
                    </div>

                    <div
                        class="flex h-full w-full items-center justify-center"
                        x-show="isLoading"
                    >
                        <x-filament::loading-indicator class="h-12 w-12" />

                        Loading messages
                    </div>

                    <div
                        class="divide-y dark:divide-gray-800"
                        x-cloak
                    >
                        <template x-for="message in messages">
                            <div class="group w-full bg-white dark:bg-gray-900">
                                <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                    <div
                                        class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                        <div class="relative flex flex-shrink-0 flex-col items-end">
                                            <img
                                                class="h-8 w-8 rounded-full object-cover object-center"
                                                x-bind:src="message.user_id ? (users[message.user_id]?.avatar_url ??
                                                    @js(filament()->getUserAvatarUrl(auth()->user()))) : @js($this->thread->assistant->getFirstTemporaryUrl(now()->addHour(), 'avatar') ?: \Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg'))"
                                                x-bind:alt="message.user_id ? (users[message.user_id]?.name ?? @js(auth()->user()->name . ' avatar')) :
                                                    @js($this->thread->assistant->name . ' avatar')"
                                            />
                                        </div>
                                        <div
                                            class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                            <div class="flex max-w-full flex-grow flex-col gap-3">
                                                <div
                                                    class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                    <div
                                                        class="prose dark:prose-invert"
                                                        x-html="message.content"
                                                    ></div>
                                                </div>
                                            </div>
                                            <div class="flex justify-between empty:hidden lg:block">
                                                <div
                                                    class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3 lg:absolute lg:right-0 lg:top-0 lg:mt-0 lg:translate-x-full lg:gap-1 lg:self-center lg:pl-2"
                                                    x-data="{
                                                        messageCopied: false,
                                                        copyMessage: function() {
                                                            navigator.clipboard.writeText(message.content.replace(/(<([^>]+)>)/gi, ''))
                                                    
                                                            this.messageCopied = true
                                                    
                                                            setTimeout(() => { this.messageCopied = false }, 2000)
                                                        }
                                                    }"
                                                >
                                                    <x-filament::icon
                                                        class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2 rounded-md p-1 text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400"
                                                        icon="heroicon-o-clipboard-document-check"
                                                        x-show="messageCopied"
                                                    />
                                                    <x-filament::icon
                                                        class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2 rounded-md p-1 text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400"
                                                        icon="heroicon-o-clipboard"
                                                        x-show="! messageCopied"
                                                        x-on:click="copyMessage"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <form x-on:submit.prevent="sendMessage">
                    <div
                        class="w-full overflow-hidden rounded-xl border border-gray-950/5 bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700">
                        <div class="bg-white dark:bg-gray-800">
                            <label
                                class="sr-only"
                                for="message_input"
                            >Type here</label>
                            <textarea
                                class="w-full resize-none border-0 bg-white p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                                id="message_input"
                                x-ref="messageInput"
                                x-model="message"
                                x-on:set-chat-message.window="message = $event.detail.content"
                                x-on:input="render()"
                                x-intersect.once="render()"
                                x-on:resize.window="render()"
                                x-on:keydown.enter="$event.shiftKey || $event.preventDefault() || sendMessage()"
                                x-bind:disabled="isSendingMessage"
                                placeholder="Type here..."
                                required
                            ></textarea>
                        </div>
                        <div
                            class="flex flex-col items-center border-t px-3 py-2 dark:border-gray-600 sm:flex-row sm:justify-between">
                            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                                <x-filament::button
                                    class="w-full sm:w-auto"
                                    type="submit"
                                >
                                    Send
                                </x-filament::button>

                                {{ $this->insertFromPromptLibraryAction }}

                                <div
                                    class="py-2"
                                    x-show="isSendingMessage"
                                >
                                    <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
                                </div>
                            </div>

                            @if (blank($this->thread->name))
                                <div class="flex w-full justify-center pt-3 sm:w-auto sm:pl-2 sm:pt-0">
                                    {{ $this->saveThreadAction }}
                                </div>
                            @else
                                <div class="flex w-full justify-center gap-1.5 pt-3 sm:w-auto sm:pt-0">
                                    {{ ($this->cloneThreadAction)(['thread' => $this->thread->id]) }}
                                    {{ ($this->emailThreadAction)(['thread' => $this->thread->id]) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        @elseif (!$this->isConsented)
            <div x-init="$nextTick(() => $dispatch('open-modal', { id: 'consent-agreement' }))">
                <x-filament::modal
                    id="consent-agreement"
                    width="5xl"
                    alignment="left"
                    :close-by-clicking-away="false"
                    :close-button="false"
                >
                    <x-slot name="header">
                        <h2 class="text-left text-xl font-semibold text-gray-950 dark:text-white">
                            {{ $this->consentAgreement->title }}
                        </h2>
                    </x-slot>

                    <div class="prose max-w-none text-left dark:prose-invert">
                        {{ str($this->consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString() }}
                    </div>

                    <x-filament::section>
                        <div class="prose max-w-none text-left text-[.7rem] leading-4 dark:prose-invert">
                            {{ str($this->consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString() }}
                        </div>
                    </x-filament::section>

                    <x-slot name="footer">
                        <form
                            class="flex w-full flex-col gap-6"
                            wire:submit="confirmConsent"
                        >
                            <label>
                                <x-filament::input.checkbox required />

                                <span class="ml-2 text-sm font-medium">
                                    I agree to the terms and conditions
                                </span>
                            </label>

                            <div class="flex justify-start gap-3">
                                <x-filament::button
                                    wire:click="denyConsent"
                                    outlined
                                >
                                    Cancel
                                </x-filament::button>
                                <x-filament::button type="submit">
                                    Continue
                                </x-filament::button>
                            </div>
                        </form>
                    </x-slot>
                </x-filament::modal>
            </div>
        @elseif (!$this->thread)
            <div
                class="flex h-full w-full items-center justify-center"
                wire:init="loadFirstThread"
            >
                <x-filament::loading-indicator class="h-12 w-12" />
            </div>
    @endif

    <script src="{{ url('js/canyon-gbs/ai/chat.js') }}"></script>
    <script src="{{ url('js/canyon-gbs/ai/chats.js') }}"></script>
    <style>
        .choices__inner .prompt-upvotes-count {
            display: none;
        }
    </style>
</div>
