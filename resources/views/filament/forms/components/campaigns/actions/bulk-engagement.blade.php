@php
    use Carbon\Carbon;
@endphp

<x-filament::fieldset>
    <x-slot name="label">
        Bulk Engagement
    </x-slot>

    <dl class="max-w-md divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
        <div class="flex flex-col pb-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Delivery Methods</dt>
            <dd class="flex flex-row space-x-2 text-sm font-semibold">
                @foreach ($action['delivery_methods'] as $deliveryMethod)
                    <x-filament::badge>
                        {{ $deliveryMethod }}
                    </x-filament::badge>
                @endforeach
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Subject</dt>
            <dd class="text-sm font-semibold">{{ $action['subject'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Body</dt>
            <dd class="text-sm font-semibold">{{ $action['body'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Execute At</dt>
            <dd class="text-sm font-semibold">{{ Carbon::parse($action['execute_at'])->format('m/d/Y H:i:s') }}</dd>
        </div>
    </dl>
</x-filament::fieldset>
