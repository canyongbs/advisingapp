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
@php
    use AdvisingApp\Alert\Enums\AlertSeverity;
@endphp
<x-filament-widgets::widget>
    <x-filament::section class="h-full">
        <x-slot name="heading">
            Alerts
        </x-slot>

        <x-slot name="headerActions">
            <x-filament::button
                color="gray"
                tag="a"
                :href="$this->getAlertsUrl()"
            >
                Manage
            </x-filament::button>
        </x-slot>

        @if ($statusCounts = $this->getStatusCounts())
            <dl class="flex flex-wrap gap-3">
                @foreach ($statusCounts as $count)
                    @php
                        $filteredUrl = $this->getAlertsUrl(['status_id' => ['value' => $count['id']]]);
                    @endphp
                    <a href="{{ $filteredUrl }}">
                        <div
                            class="flex min-w-24 flex-col items-center rounded-lg bg-gray-950/5 p-3 transition hover:bg-gray-200 dark:bg-gray-950 dark:hover:bg-gray-800">
                            <dd class="text-3xl font-semibold">
                                {{ $count['alert_count'] }}
                            </dd>

                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                {{ Str::title($count['classification']) }}
                            </dt>
                        </div>
                    </a>
                @endforeach
            </dl>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
