@php
    if(! function_exists('backgroundColor')) {
        function backgroundColor($status) {
            return match ($status) {
                Spatie\Health\Enums\Status::ok()->value => 'bg-emerald-100',
                Spatie\Health\Enums\Status::warning()->value => 'bg-yellow-100',
                Spatie\Health\Enums\Status::skipped()->value => 'bg-blue-100',
                Spatie\Health\Enums\Status::failed()->value, Spatie\Health\Enums\Status::crashed()->value => 'bg-red-100',
                default => 'bg-gray-100'
            };
        }
    }

    if(! function_exists('iconColor')) {
        function iconColor($status)
        {
            return match ($status) {
                Spatie\Health\Enums\Status::ok()->value => 'text-emerald-500',
                Spatie\Health\Enums\Status::warning()->value => 'text-yellow-500',
                Spatie\Health\Enums\Status::skipped()->value => 'text-blue-500',
                Spatie\Health\Enums\Status::failed()->value, Spatie\Health\Enums\Status::crashed()->value => 'text-red-500',
                default => 'text-gray-500'
            };
        }
    }

    if(! function_exists('icon')) {
        function icon($status)
        {
            return match ($status) {
                Spatie\Health\Enums\Status::ok()->value => 'check-circle',
                Spatie\Health\Enums\Status::warning()->value => 'exclamation-circle',
                Spatie\Health\Enums\Status::skipped()->value => 'arrow-circle-right',
                Spatie\Health\Enums\Status::failed()->value, Spatie\Health\Enums\Status::crashed()->value => 'x-circle',
                default => ''
            };
        }
    }
@endphp

<x-filament-panels::page>
    <div class="filament-spatie-health">
    	@if (count($checkResults?->storedCheckResults ?? []))
    		<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-5">
    			@foreach ($checkResults->storedCheckResults as $result)
    				<div class="flex items-start px-4 space-x-2 overflow-hidden py-5 text-opacity-0 transition transform bg-white shadow-sm shadow-gray-200 dark:shadow-black/25 dark:shadow-md dark:bg-gray-900 rounded-xl sm:p-6 md:space-x-3 md:min-h-[130px] dark:border-t dark:border-gray-700">
    					<div class="flex justify-center items-center rounded-full p-2 {{ backgroundColor($result->status) }}">
    						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ iconColor($result->status) }}" viewBox="0 0 20 20" fill="currentColor">
    							@if(icon($result->status) == 'check-circle')
    								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    							@elseif(icon($result->status) == 'exclamation-circle')
    								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
    							@elseif(icon($result->status) == 'arrow-circle-right')
    								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
    							@elseif(icon($result->status) == 'x-circle')
    								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    							@else
    								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
    							@endif
    						</svg>
    					</div>

    					<div>
    						<dd class="-mt-1 font-bold text-gray-900 dark:text-white md:mt-1 md:text-xl">
    							{{ $result->label }}
    						</dd>
    						<dt class="mt-0 text-sm font-medium text-gray-600 dark:text-gray-400 md:mt-1">
    							@if (!empty($result->notificationMessage))
    								{{ $result->notificationMessage }}
    							@else
    								{{ $result->shortSummary }}
    							@endif
    						</dt>
    					</div>
    				</div>
    			@endforeach
    		</div>
    	@endif

    	@if ($lastRanAt)
            <div class="{{ $lastRanAt->diffInMinutes() > 5 ? 'text-red-500' : 'text-gray-400 dark:text-gray-200' }} text-md text-center font-medium">
                {{ __('filament-spatie-health::health.pages.health_check_results.notifications.check_results') }} {{ $lastRanAt->diffForHumans() }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
