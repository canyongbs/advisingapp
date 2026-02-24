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
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Notifications</x-slot>

        @php
            $notifications = $this->getNotifications();
        @endphp

        @if ($notifications->count())
            <div
                class="-mx-6 -mt-6 divide-y divide-gray-200 border-b border-gray-200 dark:divide-white/10 dark:border-white/10"
            >
                @foreach ($notifications as $notification)
                    <div
                        @class([
                            'relative before:absolute before:start-0 before:h-full before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500' => $notification->unread(),
                        ])
                    >
                        {{ $this->getNotification($notification)->inline() }}
                    </div>
                @endforeach
            </div>

            <x-filament::pagination class="mt-6" :paginator="$notifications" />
        @else
            <div class="flex flex-col px-6">
                <div class="mb-5 flex items-center justify-center">
                    <div class="dark:bg-gray-500/20 rounded-full bg-gray-100 p-3">
                        <x-heroicon-o-bell-slash class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">No notifications</h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Please check again later.</p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
