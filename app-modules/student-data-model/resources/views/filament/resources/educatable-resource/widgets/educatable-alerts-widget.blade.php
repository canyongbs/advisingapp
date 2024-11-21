<x-filament-widgets::widget>
    <x-filament::section class="h-full">
        <x-slot name="heading">
            Alerts

            @if ($activeCount = $this->getActiveCount())
                <span class="font-normal text-gray-500 dark:text-gray-400">({{ $activeCount }} Active)</span>
            @endif
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

        @if ($severityCounts = $this->getSeverityCounts())
            <dl class="flex flex-wrap gap-3">
                @foreach ($severityCounts as $severity => $count)
                    <div class="flex min-w-24 flex-col items-center rounded-lg bg-gray-950/5 p-3 dark:bg-gray-950">
                        <dd class="text-3xl font-semibold">
                            {{ $count }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ $severity }}
                        </dt>
                    </div>
                @endforeach
            </dl>
        @else
            <div class="p-6">
                <div class="mx-auto grid max-w-lg justify-items-center gap-4 text-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        @svg('heroicon-o-bell-slash', 'h-6 w-6 text-gray-500 dark:text-gray-400')
                    </div>

                    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No alerts
                    </h4>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
