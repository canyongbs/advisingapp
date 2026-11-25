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
    use AdvisingApp\Authorization\Filament\Pages\Auth\EmailFirstLogin;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;

    $onEmailStep = $this->step === 'email';
@endphp

<div
    class="mx-auto flex w-full max-w-2xl flex-col gap-4"
    x-data
    x-on:login-password-step.window="$nextTick(() => document.querySelector('input[type=password]')?.focus())"
>
    <section
        class="flex flex-col rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 sm:p-8 dark:bg-gray-900 dark:ring-white/10"
    >
        <div class="bg-primary-500 mb-6 h-1 w-10 rounded-full"></div>

        <p class="text-sm text-gray-500 dark:text-gray-400">Good to see you again.</p>

        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-950 sm:text-3xl dark:text-white">
            Welcome to {{ config('app.name') }}
        </h1>

        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            @if ($onEmailStep)
                Enter your email address to continue. We'll guide you to the right sign-in method based on your account.
            @else
                Enter your password to finish signing in.
            @endif
        </p>

        <form class="mt-6 flex flex-col gap-6" wire:submit="{{ $onEmailStep ? 'continue' : 'authenticate' }}">
            {{ $this->form }}

            <x-filament::actions :actions="$this->getFormActions()" full-width />

            @unless ($onEmailStep)
                <div class="flex justify-center">
                    <x-filament::link tag="button" type="button" color="gray" wire:click="backToEmailStep">
                        Use a different email
                    </x-filament::link>
                </div>
            @endunless
        </form>

        <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <x-filament::icon
                :icon="Heroicon::ShieldCheck"
                :size="IconSize::Small"
                class="shrink-0 text-gray-400 dark:text-gray-500"
            />
            <span>Your information is secure. We use industry-standard security practices to protect your data.</span>
        </div>
    </section>

    @if ($this->isMobileApp())
        <x-filament::actions class="mt-6" :full-width="true">
            <x-filament::button tag="a" :href="EmailFirstLogin::SWITCH_TENANT_URL" color="gray">
                Switch tenant
            </x-filament::button>
        </x-filament::actions>
    @endif
</div>
