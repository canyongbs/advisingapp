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
@php
    use AdvisingApp\Engagement\Models\Engagement;
    use AdvisingApp\Prospect\Models\ProspectEmailAddress;
    use AdvisingApp\StudentDataModel\Enums\EmailHealthStatus;
    use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;

    $healthStatus = $emailAddress->getHealthStatus();

    $isBounced = $healthStatus === EmailHealthStatus::Bounced;
    $isOptedOut = $healthStatus === EmailHealthStatus::OptedOut;
    $isDisabled = $isBounced || $isOptedOut;
@endphp

<button
    class="flex items-start gap-2 break-all text-left"
    type="button"
    x-data="{ isLoading: false }"
    x-on:engage-action-finished-loading.window="isLoading = false"
    x-on:click="
        isLoading = true
        $dispatch('send-email', { emailAddressKey: @js($emailAddress->getKey()) })
    "
    @disabled(
        $isDisabled ||
        ! auth()
            ->user()
            ->can('create', [
                Engagement::class,
                $emailAddress instanceof ProspectEmailAddress ? $emailAddress->prospect : null,
            ])
    )
>
    <div class="mt-1">
        @svg('heroicon-m-envelope', 'size-5', ['x-show' => '! isLoading'])
    </div>

    <x-filament::loading-indicator class="size-5" x-show="isLoading" x-cloak />

    <span @if (!$isDisabled) x-tooltip.raw="Click to send an email" @endif>
        {{ $emailAddress->address }}

        @if (filled($emailAddress->type))
            ({{ $emailAddress->type }})
        @endif
    </span>

    <x-filament::icon
        class="ml-1 h-6 w-6"
        style="color: {{ $healthStatus->getLightModeColor() }};"
        x-bind:style="$store.theme === 'dark' ? 'color: {{ $healthStatus->getDarkModeColor() }};' :
            'color: {{ $healthStatus->getLightModeColor() }};'"
        icon="{{ $healthStatus->getIcon() }}"
        x-tooltip.raw="{{ $healthStatus->getTooltipText() }}"
    />
</button>
