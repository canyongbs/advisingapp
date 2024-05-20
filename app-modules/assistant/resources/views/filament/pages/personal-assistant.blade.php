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
<?php

use AdvisingApp\Assistant\Models\AiAssistant;
use AdvisingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;

?>

<x-filament-panels::page full-height="true">
    <div
        class="flex h-full flex-col"
    >
        @if ($this->isConsented && $thread)
            <div
                x-data="chats"
                class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4"
            >
                <div class="col-span-1">
                    <div class="flex flex-col h-screen max-h-[calc(100dvh-20rem)]"></div>
{{--                    <div class="flex flex-col gap-y-2">--}}
{{--                        @if (--}}
{{--                            \Illuminate\Support\Facades\Gate::check(\App\Enums\Feature::CustomAiAssistants->getGateName()) &&--}}
{{--                                count($assistants = AiAssistant::all()) > 1)--}}
{{--                            <x-filament::dropdown>--}}
{{--                                <x-slot name="trigger">--}}
{{--                                    <x-filament::button--}}
{{--                                        class="w-full"--}}
{{--                                        icon="heroicon-m-plus"--}}
{{--                                    >--}}
{{--                                        {{ __('New Chat') }}--}}
{{--                                    </x-filament::button>--}}
{{--                                </x-slot>--}}

{{--                                <x-filament::dropdown.header>--}}
{{--                                    Choose an assistant to use--}}
{{--                                </x-filament::dropdown.header>--}}

{{--                                <x-filament::dropdown.list>--}}
{{--                                    @foreach ($assistants as $assistant)--}}
{{--                                        <x-filament::dropdown.list.item--}}
{{--                                            wire:click="newChatWithAssistant('{{ $assistant->id }}')"--}}
{{--                                        >--}}
{{--                                            {{ $assistant->name }}--}}
{{--                                        </x-filament::dropdown.list.item>--}}
{{--                                    @endforeach--}}
{{--                                </x-filament::dropdown.list>--}}
{{--                            </x-filament::dropdown>--}}
{{--                        @else--}}
{{--                            <x-filament::button--}}
{{--                                icon="heroicon-m-plus"--}}
{{--                                wire:click="newChat"--}}
{{--                            >--}}
{{--                                {{ __('New Chat') }}--}}
{{--                            </x-filament::button>--}}
{{--                        @endif--}}

{{--                        {{ $this->newFolderAction }}--}}

{{--                        @if (count($this->threadsWithoutAFolder))--}}
{{--                            <ul--}}
{{--                                class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"--}}
{{--                                id="folder-{{ null }}"--}}
{{--                                @drop.prevent="drop('{{ null }}')"--}}
{{--                                @dragenter.prevent--}}
{{--                                @dragover.prevent--}}
{{--                            >--}}
{{--                                @foreach ($this->threadsWithoutAFolder as $threadItem)--}}
{{--                                    <li--}}
{{--                                        id="chat-{{ $threadItem->id }}"--}}
{{--                                        draggable="true"--}}
{{--                                        @dragstart="start('{{ $threadItem->id }}', '{{ null }}')"--}}
{{--                                        @dragend="end"--}}
{{--                                        wire:key="chat-{{ $threadItem->id }}"--}}
{{--                                        @class([--}}
{{--                                            'px-2 group cursor-move flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',--}}
{{--                                            'bg-gray-100 dark:bg-white/5' => $thread->id === $threadItem->id,--}}
{{--                                        ])--}}
{{--                                    >--}}
{{--                                        <a--}}
{{--                                            @class([--}}
{{--                                                'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm',--}}
{{--                                            ])--}}
{{--                                            wire:click="selectThread('{{ $threadItem->id }}')"--}}
{{--                                        >--}}
{{--                                            <span @class([--}}
{{--                                                'fi-sidebar-item-label flex-1 truncate',--}}
{{--                                                'text-gray-700 dark:text-gray-200' => !$thread->id === $threadItem->id,--}}
{{--                                                'text-primary-600 dark:text-primary-400' => $thread->id === $threadItem->id,--}}
{{--                                            ])>--}}
{{--                                                {{ $threadItem->name }}--}}
{{--                                            </span>--}}
{{--                                        </a>--}}

{{--                                        <div>--}}
{{--                                            {{ ($this->moveThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                            {{ ($this->editThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                            {{ ($this->deleteThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                        </div>--}}
{{--                                    </li>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}
{{--                        @else--}}
{{--                            <div--}}
{{--                                class="flex flex-col gap-y-1 rounded-xl border border-dashed border-gray-950/5 bg-white p-2 text-gray-500 shadow-sm dark:border-white/10 dark:bg-gray-900"--}}
{{--                                x-show="dragging"--}}
{{--                                @drop.prevent="drop('{{ null }}')"--}}
{{--                                @dragenter.prevent--}}
{{--                                @dragover.prevent--}}
{{--                            >--}}
{{--                                <div>--}}
{{--                                    Drag chats here to uncategorize them--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        @if (count($this->folders))--}}
{{--                            @foreach ($this->folders as $folder)--}}
{{--                                <ul--}}
{{--                                    class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"--}}
{{--                                    id="folder-{{ $folder->id }}"--}}
{{--                                    @drop.prevent="drop('{{ $folder->id }}')"--}}
{{--                                    @dragenter.prevent--}}
{{--                                    @dragover.prevent--}}
{{--                                >--}}
{{--                                    <span--}}
{{--                                        class='group flex w-full cursor-move items-center rounded-lg px-2 outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5'--}}
{{--                                    >--}}
{{--                                        <x-filament::icon-button--}}
{{--                                            icon="heroicon-o-folder-open"--}}
{{--                                            x-show="expanded('{{ $folder->id }}')"--}}
{{--                                        />--}}
{{--                                        <x-filament::icon-button--}}
{{--                                            icon="heroicon-o-folder"--}}
{{--                                            x-show="expanded('{{ $folder->id }}') === false"--}}
{{--                                        />--}}
{{--                                        <span--}}
{{--                                            class='group flex w-full cursor-pointer items-center space-x-1 rounded-lg px-2 outline-none transition duration-75 focus:bg-gray-100 dark:focus:bg-white/5'--}}
{{--                                        >--}}
{{--                                            <span--}}
{{--                                                class='fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm'--}}
{{--                                                @click="expand('{{ $folder->id }}')"--}}
{{--                                            >--}}
{{--                                                @if ($folder->chats->count())--}}
{{--                                                    <span class='fi-sidebar-item-label flex-1 truncate'>--}}
{{--                                                        {{ $folder->name }} ({{ $folder->chats->count() }})--}}
{{--                                                    </span>--}}
{{--                                                @else--}}
{{--                                                    <span class='fi-sidebar-item-label flex-1 truncate'>--}}
{{--                                                        {{ $folder->name }}--}}
{{--                                                    </span>--}}
{{--                                                @endif--}}
{{--                                            </span>--}}

{{--                                            <span>--}}
{{--                                                {{ ($this->renameFolderAction)(['folder' => $folder->id]) }}--}}
{{--                                                {{ ($this->deleteFolderAction)(['folder' => $folder->id]) }}--}}
{{--                                            </span>--}}
{{--                                        </span>--}}
{{--                                    </span>--}}
{{--                                    @foreach ($folder->chats as $threadItem)--}}
{{--                                        <li--}}
{{--                                            id="chat-{{ $threadItem->id }}"--}}
{{--                                            @class([--}}
{{--                                                'px-2 group cursor-move flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',--}}
{{--                                                'bg-gray-100 dark:bg-white/5' => $thread->id === $threadItem->id,--}}
{{--                                            ])--}}
{{--                                            draggable="true"--}}
{{--                                            @dragstart="start('{{ $threadItem->id }}', '{{ $folder->id }}')"--}}
{{--                                            @dragend="end"--}}
{{--                                            wire:key="chat-{{ $threadItem->id }}"--}}
{{--                                            x-show="expanded('{{ $folder->id }}')"--}}
{{--                                        >--}}
{{--                                            <a--}}
{{--                                                @class([--}}
{{--                                                    'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm',--}}
{{--                                                ])--}}
{{--                                                wire:click="selectThread('{{ $threadItem->id }}')"--}}
{{--                                            >--}}
{{--                                                <span @class([--}}
{{--                                                    'fi-sidebar-item-label flex-1 truncate',--}}
{{--                                                    'text-gray-700 dark:text-gray-200' => !$thread->id === $threadItem->id,--}}
{{--                                                    'text-primary-600 dark:text-primary-400' => $thread->id === $threadItem->id,--}}
{{--                                                ])>--}}
{{--                                                    {{ $threadItem->name }}--}}
{{--                                                </span>--}}
{{--                                            </a>--}}

{{--                                            <div>--}}
{{--                                                {{ ($this->moveThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                                {{ ($this->editThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                                {{ ($this->deleteThreadAction)(['chat' => $threadItem->id]) }}--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                    @endforeach--}}
{{--                                </ul>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </div>--}}
                </div>

                <div
                    x-data="chat({
                        csrfToken: @js(csrf_token()),
                        retryMessageUrl: @js(route('ai.threads.messages.retry', ['thread' => $thread])),
                        sendMessageUrl: @js(route('ai.threads.messages.send', ['thread' => $thread])),
                        showThreadUrl: @js(route('ai.threads.show', ['thread' => $thread])),
                        userId: @js(auth()->user()->id),
                    })"
                    wire:key="thread{{ $thread->id }}"
                    class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                >
                    <h1>{{ $thread->assistant->name }}</h1>

                    <div
                        class="flex max-h-[calc(100dvh-20rem)] overflow-y-scroll flex-1 flex-col-reverse rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
                        x-ref="chatContainer"
                    >
                        <div x-cloak x-show="isError" class="bg-danger-100 dark:bg-danger-900 px-4 py-2">
                            An error happened when sending your message, <x-filament::link x-on:click="retryMessage" tag="button" color="gray">click here to retry.</x-filament::link>
                        </div>

                        <div x-show="isLoading" class="flex h-full w-full items-center justify-center">
                            <x-filament::loading-indicator class="h-12 w-12" />

                            Loading messages
                        </div>

                        <div x-cloak class="divide-y dark:divide-gray-800">
                            <template x-for="message in messages">
                                <div class="group w-full bg-white dark:bg-gray-900">
                                    <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                        <div
                                            class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                            <div class="relative flex flex-shrink-0 flex-col items-end">
                                                <img
                                                    x-bind:src="message.user_id ? (users[message.user_id]?.avatar_url ?? @js(filament()->getUserAvatarUrl(auth()->user()))) : @js($thread->assistant->getFirstTemporaryUrl(now()->addHour(), 'avatar') ?: Vite::asset('resources/images/canyon-ai-headshot.jpg'))"
                                                    x-bind:alt="message.user_id ? (users[message.user_id]?.name ?? @js(auth()->user()->name . ' avatar')) : @js($thread->assistant->name . ' avatar')"
                                                    class="object-cover object-center rounded-full h-8 w-8"
                                                />
                                            </div>
                                            <div class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                <div class="flex max-w-full flex-grow flex-col gap-3">
                                                    <div
                                                        class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                        <div x-html="message.content" class="prose dark:prose-invert"></div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between empty:hidden lg:block">
                                                    <div
                                                        class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3 lg:absolute lg:right-0 lg:top-0 lg:mt-0 lg:translate-x-full lg:gap-1 lg:self-center lg:pl-2"
                                                        x-data="{
                                                             messageCopied: false,
                                                             copyMessage: function () {
                                                                 navigator.clipboard.writeText(message.content)

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
                                    x-on:input="render()"
                                    x-intersect.once="render()"
                                    x-on:resize.window="render()"
                                    x-on:message-sent.window="$nextTick(render)"
                                    x-on:keydown.enter="$event.shiftKey || $event.preventDefault() || sendMessage()"
                                    x-bind:disabled="isSendingMessage"
                                    placeholder="Type here..."
                                    required
                                ></textarea>
                            </div>
                            <div class="flex items-center justify-between border-t px-3 py-2 dark:border-gray-600">
                                <div class="flex w-full items-center gap-3">
                                    <x-filament::button type="submit">
                                        Send
                                    </x-filament::button>

                                    {{ $this->insertFromPromptLibraryAction }}

                                    <div
                                        x-show="isSendingMessage"
                                        class="py-2"
                                    >
                                        <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
                                    </div>
                                </div>

                                @if (blank($thread->name))
                                    <div
                                        class="flex pl-0 sm:pl-2"
                                    >
                                        {{ $this->saveThreadAction }}
                                    </div>
                                @else
                                    <div class="flex gap-3">
                                        {{ ($this->cloneThreadAction)(['thread' => $thread->id]) }}
                                        {{ ($this->emailThreadAction)(['thread' => $thread->id]) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            @elseif (! $this->isConsented)
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
            @elseif (! $thread)
                <div wire:init="loadFirstThread" class="flex h-full w-full items-center justify-center">
                    <x-filament::loading-indicator class="h-12 w-12" />
                </div>
            @endif

            <script src="{{ url('js/canyon-gbs/assistant/chat.js') }}"></script>
            <script src="{{ url('js/canyon-gbs/assistant/chats.js') }}"></script>
            <style>
                .choices__inner .prompt-upvotes-count {
                    display: none;
                }
            </style>
        </div>
    </x-filament-panels::page>
