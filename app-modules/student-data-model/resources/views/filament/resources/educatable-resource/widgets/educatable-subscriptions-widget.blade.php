{{--
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
--}}
<x-filament-widgets::widget>
    @php
        $subscribedUsers = $this->getSubscribedUsers();
    @endphp

    <x-filament::section @class([
        'fi-section-has-subsections h-full',
        'fi-scrollable' => $subscribedUsers,
    ])>
        <x-slot name="heading">
            Subscriptions
        </x-slot>

        <x-slot name="headerActions">
            <x-filament::button
                color="gray"
                tag="a"
                :href="$manageUrl"
            >
                Manage
            </x-filament::button>
        </x-slot>

        @forelse ($subscribedUsers as $subscriber)
            <div class="flex w-full items-center gap-6 px-6 py-3">
                <x-filament::avatar
                    class="shrink-0"
                    :src="filament()->getUserAvatarUrl($subscriber)"
                    loading="lazy"
                    size="lg"
                />

                <div class="grid flex-1 gap-y-0.5">
                    <p class="font-medium text-gray-950 dark:text-white">
                        {{ $subscriber->name }}
                    </p>

                    @if (filled($subscriber->job_title))
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Subscribed {{ $subscriber->pivot->created_at->toFormattedDateString() }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6">
                <div class="mx-auto grid max-w-lg justify-items-center gap-4 text-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        @svg('heroicon-o-x-mark', 'h-6 w-6 text-gray-500 dark:text-gray-400')
                    </div>

                    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No subscriptions
                    </h4>
                </div>
            </div>
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>
