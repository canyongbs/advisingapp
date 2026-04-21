{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.
    
    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
    same in return. Canyon GBS® and Advising App® are registered trademarks of
    Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
@php
    use AdvisingApp\Engagement\Models\Engagement;
    use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
    use AdvisingApp\StudentDataModel\Enums\PhoneHealthStatus;
    use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;

    $healthStatus = $phoneNumber->getHealthStatus();

    $isDisabled = $healthStatus !== PhoneHealthStatus::Healthy;
@endphp

<button
    class="flex items-start gap-2 break-all text-left"
    type="button"
    x-data="{ isLoading: false }"
    x-on:engage-action-finished-loading.window="isLoading = false"
    x-on:click="
        isLoading = true
        $dispatch('send-sms', { phoneNumberKey: @js($phoneNumber->getKey()) })
    "
    @disabled(
        $isDisabled ||
        ! auth()
            ->user()
            ->can('create', [
                Engagement::class,
                $phoneNumber instanceof ProspectPhoneNumber ? $phoneNumber->prospect : null,
            ])
    )
>
    @svg('heroicon-m-phone', 'h-5 w-5 shrink-0', ['x-show' => '! isLoading'])

    <x-filament::loading-indicator class="h-5 w-5 shrink-0" x-show="isLoading" x-cloak />

    <span @if (!$isDisabled) x-tooltip.raw="Click to send an SMS" @endif>
        {{ $phoneNumber->number }}

        @if (filled($phoneNumber->ext))
            (ext. {{ $phoneNumber->ext }})
        @endif

        @if (filled($phoneNumber->type))
            ({{ $phoneNumber->type }})
        @endif
    </span>

    <x-filament::icon
        class="h-5 w-5 shrink-0"
        style="color: {{ $healthStatus->getLightModeColor() }};"
        x-bind:style="$store.theme === 'dark' ? 'color: {{ $healthStatus->getDarkModeColor() }};' :
            'color: {{ $healthStatus->getLightModeColor() }};'"
        icon="{{ $healthStatus->getIcon() }}"
        x-tooltip.raw="{{ $healthStatus->getTooltipText() }}"
    />
</button>
