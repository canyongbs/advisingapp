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
<div class="space-y-3 px-4">
    <p class="text-xs">
        These codes can be used to recover access to your account if your device is lost. Warning! These codes will only
        be shown once.
    </p>
    <div>
        @foreach ($codes->toArray() as $code)
            <span
                class="inline-flex items-center rounded-full bg-gray-100 p-1 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-400"
            >
                {{ $code }}
            </span>
        @endforeach
    </div>
    <div class="inline-block text-xs">
        <div
            class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3"
            x-data="{
                messageCopied: false,
                copyMessage: function () {
                    navigator.clipboard.writeText(@js($codes->join(',')))

                    this.messageCopied = true

                    setTimeout(() => {
                        this.messageCopied = false
                    }, 2000)
                },
            }"
        >
            <span
                class="flex cursor-pointer items-center rounded-md p-1 text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400"
                x-on:click="copyMessage"
            >
                <x-filament::icon
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2"
                    icon="heroicon-o-clipboard-document-check"
                    x-show="messageCopied"
                />
                <x-filament::icon
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2"
                    icon="heroicon-o-clipboard"
                    x-show="! messageCopied"
                />
                <span class="">Copy to clipboard</span>
            </span>
        </div>
    </div>
</div>
