{{--
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
--}}
@php
use AdvisingApp\InAppCommunication\Enums\ConversationType;
use Filament\Support\Facades\FilamentAsset;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;
@endphp

<x-filament-panels::page full-height="true">
    <div class="flex h-full flex-col">
        <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4">
            <div class="col-span-1">
                <div class="flex flex-col gap-y-2">
                    {{ $this->newUserToUserChatAction }}
                    {{ $this->newChannelAction }}

                    @if ($this->conversations->isNotEmpty())
                        <ul
                            class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            @foreach ($this->conversations as $conversation)
                                @php
                                    /** @var TwilioConversation $conversation */
                                @endphp
                                <li @class([
                                    'px-2 group cursor-pointer flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                    'bg-gray-100 dark:bg-white/5' =>
                                        $selectedConversation === $conversation['sid'],
                                ])>
                                    <button
                                        @class([
                                            'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm',
                                        ])
                                        type="button"
                                        wire:click="selectConversation('{{ $conversation['sid'] }}')"
                                    >
                                        <span @class([
                                            'fi-sidebar-item-label flex-1 truncate',
                                            'text-gray-700 dark:text-gray-200' => $selectedConversation !== $conversation['sid'],
                                            'text-primary-600 dark:text-primary-400' => $selectedConversation === $conversation['sid'],
                                        ])>
                                            @if (filled($conversation->channel_name))
                                                {{ $conversation->channel_name }}
                                            @else
                                                {{ $conversation->participants->where('user_id', '!=', auth()->id())->first()->name }}
                                            @endif
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            @if (!is_null($selectedConversation))
                <div
                    class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                    x-data="userToUserChat(`{{ $selectedConversation }}`)"
                    wire:key="conversation-{{ $selectedConversation }}"
                >
                    <div
                        class="flex flex-col items-center self-center"
                        x-show="loading"
                        x-transition.delay.800ms
                    >
                        <x-filament::loading-indicator class="h-12 w-12 text-primary-500"/>
                        <p
                            class="text-center"
                            x-text="loadingMessage"
                        ></p>
                    </div>
                    <template x-if="!loading && error">
                        <div class="flex flex-col items-center self-center">
                            <x-filament::icon
                                class="h-12 w-12 text-primary-500"
                                icon="heroicon-m-exclamation-triangle"
                            />
                            <p class="text-center">Something went wrong...</p>
                            <p
                                class="text-center"
                                x-text="errorMessage"
                            ></p>
                            <x-filament::button
                                class="mt-2"
                                x-on:click="errorRetry"
                            >Retry
                            </x-filament::button>
                        </div>
                    </template>
                    <div
                        class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                        x-show="!loading && !error"
                        x-transition.delay.850ms
                    >
                        <div
                            class="flex max-h-[calc(100vh-24rem)] flex-1 flex-col-reverse overflow-y-scroll rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800">
                            <div class="divide-y dark:divide-gray-700">
                                <template
                                    x-for="message in messages"
                                    :key="message.message.index"
                                >
                                    <div class="group w-full dark:bg-gray-800">
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <x-filament::avatar
                                                            class="rounded-full"
                                                            alt="User Avatar"
                                                            x-bind:src="message.avatar"
                                                        />
                                                    </div>
                                                </div>
                                                <div
                                                    class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <div x-text="message.message.body"></div>
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
                                    class="mb-auto flex cursor-pointer justify-center bg-white p-3 text-center text-primary-500 dark:bg-gray-700"
                                    x-on:click="loadPreviousMessages"
                                >
                                    <p>Load previous messages...</p>
                                    <x-filament::loading-indicator
                                        class="ml-2 h-4 w-4 text-primary-500"
                                        x-show="loadingPreviousMessages"
                                    />
                                </div>
                            </template>
                        </div>

                        <form @submit.prevent="submit">
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
                                        x-model="message"
                                        rows="4"
                                        placeholder="Type here..."
                                        required
                                        x-on:keydown="typing"
                                    >
                                </textarea>
                                </div>
                                <div class="flex items-center justify-between border-t px-3 py-2 dark:border-gray-600">
                                    <div class="flex items-center gap-3">
                                        <x-filament::button type="submit">
                                            Post
                                        </x-filament::button>
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
                                                class="h-2 w-2 animate-bounce rounded-full bg-primary-500 text-primary-500 animation-delay-100"
                                            ></span>
                                            <span
                                                class="h-2 w-2 animate-bounce rounded-full bg-primary-500 text-primary-500 animation-delay-200"
                                            ></span>
                                            <span
                                                class="h-2 w-2 animate-bounce rounded-full bg-primary-500 text-primary-500 animation-delay-300"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3">
                    <p class="text-center text-xl">Select or create a new Chat</p>
                </div>
            @endif
        </div>
        <script src="{{ FilamentAsset::getScriptSrc('userToUserChat', 'canyon-gbs/in-app-communication') }}"></script>
    </div>
</x-filament-panels::page>
