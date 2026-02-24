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
<div class="mb-3 flex gap-4 text-base md:gap-6">
    <div class="flex flex-shrink-0 flex-col items-end">
        <img class="h-8 w-8 rounded-full object-cover object-center" src="{{ $avatarUrl }}" alt="Assistant avatar" />
    </div>

    <div class="prose h-36 flex-1 dark:prose-invert sm:h-20">
        <p
            x-data="{ content: '' }"
            x-init="
                const message = @js('Hi ' . auth()->user()->name . ", I am happy to help you draft your resource hub article for {$recordTitle}. Please describe what information you would like in the article and I will take it from there:")

                const typeWord = async (word, delay) => {
                    content += word + ' '

                    await new Promise((resolve) => setTimeout(resolve, delay))
                }

                for (const word of message.split(' ')) {
                    await typeWord(word, Math.floor(Math.random() * 100))
                }
            "
            x-text="content"
        ></p>
    </div>
</div>
