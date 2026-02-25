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
@use(Illuminate\Support\Facades\URL)
@use(AdvisingApp\Theme\Settings\ThemeSettings)

@php
    $themeSettings = app(ThemeSettings::class);
@endphp

<x-filament-panels::page>
    @if ($this->isFailed)
        <x-filament::empty-state icon="heroicon-o-x-circle">
            <x-slot name="heading">QnA Advisor Failed to Learn</x-slot>

            <x-slot name="description">
                There was an issue while trying to learn from the materials you have uploaded/provided. Please
                @if ($themeSettings->is_support_url_enabled && filled($themeSettings->support_url))
                    <x-filament::link :href="$themeSettings->support_url" target="_blank">
                        contact support
                    </x-filament::link>
                @else
                    contact support
                @endif
                for assistance.
            </x-slot>
        </x-filament::empty-state>
    @elseif ($this->isProcessing)
        <x-filament::empty-state icon="heroicon-o-clock">
            <x-slot name="heading">QnA Advisor is Learning</x-slot>

            <x-slot name="description">
                This AI advisor is still learning from the materials you have uploaded/provided. Please check back in
                about 5 minutes.
            </x-slot>
        </x-filament::empty-state>
    @else
        <div
            class="flex h-[calc(100dvh-16rem)] flex-col gap-y-3"
            x-data="qnaAdvisorPreview({
                        csrfToken: @js(csrf_token()),
                        sendMessageUrl: @js(URL::to(URL::signedRoute('widgets.ai.qna-advisors.api.messages.send', ['advisor' => $this->getRecord(), 'preview' => true]))),
                        userId: @js(auth()->user()->id),
                    })"
        >
            <div
                class="border-gray-950/5 flex flex-1 flex-col-reverse overflow-y-scroll rounded-xl border text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
            >
                <div class="divide-y divide-gray-200 dark:divide-gray-800" x-cloak>
                    <template x-for="(message, messageIndex) in messages">
                        <div class="group w-full bg-white dark:bg-gray-900">
                            <div class="m-auto justify-center px-4 py-4 text-base md:gap-6 md:py-6">
                                <div
                                    class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl"
                                >
                                    <div class="relative flex flex-shrink-0 flex-col items-end">
                                        <img
                                            class="h-8 w-8 rounded-full object-cover object-center"
                                            x-bind:src="message.user_id ? @js(filament()->getUserAvatarUrl(auth()->user())) : @js(\Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg'))"
                                            x-bind:alt="message.user_id ? @js(auth()->user()->name . ' avatar') : @js($this->getRecord()->name . ' avatar')"
                                        />
                                    </div>
                                    <div
                                        class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]"
                                    >
                                        <div class="flex max-w-full flex-grow flex-col gap-3">
                                            <div
                                                class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words"
                                            >
                                                <div class="prose dark:prose-invert" x-html="message.content"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <form x-on:submit.prevent="sendMessage()">
                <div
                    class="border-gray-950/5 w-full overflow-hidden rounded-xl border bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700"
                >
                    <div class="bg-white dark:bg-gray-800">
                        <label class="sr-only" for="message_input">Type here</label>
                        <textarea
                            class="min-h-20 w-full resize-none border-0 bg-white p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                            id="message_input"
                            x-ref="messageInput"
                            x-model="message"
                            x-on:set-chat-message.window="message = $event.detail.content"
                            x-on:input="render()"
                            x-intersect.once="render()"
                            x-on:resize.window="render()"
                            x-bind:disabled="isSendingMessage"
                            placeholder="Type here..."
                            required
                            maxlength="25000"
                            @if (auth()->user()->is_submit_ai_chat_on_enter_enabled) x-on:keydown.enter="
                                if (! event.shiftKey) {
                                    event.preventDefault()
                                    sendMessage()
                                }
                            " @endif
                        ></textarea>
                    </div>
                    <div
                        class="flex flex-col items-center border-t border-gray-200 px-3 py-2 sm:flex-row sm:justify-between dark:border-gray-600"
                    >
                        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                            <x-filament::button class="w-full sm:w-auto" type="submit">Send</x-filament::button>

                            <div class="flex w-full justify-center py-2 sm:w-auto" x-show="isSendingMessage">
                                <x-filament::loading-indicator class="text-primary-500 h-5 w-5" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @vite(['app-modules/ai/resources/js/qna-advisor-preview.js'])
    @endif
</x-filament-panels::page>
