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
<div class="max-h-[600px] space-y-4 overflow-y-auto p-6">
    @forelse ($messages as $message)
        @php
            $author = $message->is_advisor ? $advisor->name : ($message->author ? $message->author->full_name : 'User');
        @endphp

        <div class="{{ $message->is_advisor ? 'justify-start' : 'justify-end' }} flex">
            <div class="max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl">
                <div
                    class="{{ $message->is_advisor ? 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' : 'bg-primary-500 text-white' }} rounded-lg px-5 py-4"
                >
                    <div
                        class="{{ $message->is_advisor ? 'text-gray-600 dark:text-gray-300' : 'text-primary-100' }} mb-3"
                    >
                        <span class="text-sm font-semibold">{{ $author }}</span>
                        <span class="ml-2 text-xs opacity-75">{{ $message->created_at->format('M j, Y g:i A') }}</span>
                    </div>

                    <div
                        class="{{ $message->is_advisor ? 'text-gray-900 dark:text-gray-100 dark:prose-invert prose-p:mb-3 prose-p:leading-relaxed' : 'text-white prose-invert prose-p:mb-3 prose-p:leading-relaxed' }} prose prose-sm max-w-none leading-relaxed"
                    >
                        {!! str($message->content)->markdown() !!}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-8 text-center">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto mb-3 h-12 w-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                    ></path>
                </svg>
                <p class="text-sm">No messages in this conversation</p>
            </div>
        </div>
    @endforelse
</div>
