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
    use AdvisingApp\Theme\Settings\ThemeSettings;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use Illuminate\Support\Str;

    $onEmailStep = $this->step === 'email';
    $onSsoStep = $this->step === 'sso';

    $onMultifactorStep = $this->needsMfa;

    $themeSettings = app(ThemeSettings::class);

    $themeChangelogUrl = ! empty($themeSettings->changelog_url) ? $themeSettings->changelog_url : ThemeSettings::DEFAULT_CHANGELOG_URL;
    $productResourceHubUrl = ! empty($themeSettings->product_resource_hub_url) ? $themeSettings->product_resource_hub_url : ThemeSettings::DEFAULT_PRODUCT_RESOURCE_HUB_URL;

    $currentVersion = app('current-version');
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
            @if ($onMultifactorStep)
                Enter the code from your authenticator app to finish signing in.
            @elseif ($onEmailStep)
                Enter your email address to continue. We'll guide you to the right sign-in method based on your account.
            @elseif ($onSsoStep)
                Choose how you'd like to sign in.
            @else
                Enter your password to finish signing in.
            @endif
        </p>

        <form
            class="mt-6 flex flex-col gap-6"
            @unless ($onSsoStep) wire:submit="{{ $onEmailStep ? 'continue' : 'authenticate' }}" @endunless
        >
            @if ($this->needsMfaSetup)
                <h3 class="flex items-center gap-2 text-lg font-medium">
                    @svg('heroicon-o-question-mark-circle', 'w-6')
                    Multifactor authentication is required for your account.
                </h3>
                <p class="text-sm">
                    To finish enabling two factor authentication, scan the following QR code using your phone's
                    authenticator application or enter the setup key and provide the generated OTP code.
                </p>
                <div class="mt-3 flex space-x-4">
                    <div>
                        {!! $this->getMultifactorQrCode() !!}
                        <p class="pt-2 text-sm">Setup key {{ decrypt($this->user->multifactor_secret) }}</p>
                    </div>
                </div>
            @endif

            {{ $this->form }}

            <x-filament::actions :actions="$this->getFormActions()" full-width />

            @if ($onSsoStep)
                <x-filament::actions :actions="[$this->azureSsoAction, $this->googleSsoAction]" full-width />
            @endif

            @if ($this->googleSsoUnavailableInMobileApp)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ EmailFirstLogin::GOOGLE_SSO_UNAVAILABLE_IN_MOBILE_APP_MESSAGE }}
                </p>
            @endif

            @if ($onMultifactorStep && ! $this->needsMfaSetup)
                <div class="flex justify-center">
                    <x-filament::link tag="button" type="button" color="gray" wire:click="toggleUsingRecoveryCodes">
                        {{ $this->usingRecoveryCode ? 'Use MFA Code' : 'Use Recovery Code' }}
                    </x-filament::link>
                </div>
            @endif

            @unless ($onEmailStep || $onMultifactorStep)
                <div class="flex justify-center">
                    <x-filament::link tag="button" type="button" color="gray" wire:click="backToEmailStep">
                        Use a different email
                    </x-filament::link>
                </div>
            @endunless
        </form>

        @if ($onEmailStep)
            <div class="mt-8 hidden sm:block">
                <div class="flex items-center gap-4">
                    <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
                    <span class="shrink-0 text-xs font-medium text-gray-500 dark:text-gray-400">
                        Here's what happens next
                    </span>
                    <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
                </div>

                <div class="mt-6 grid grid-cols-3 divide-x divide-gray-200 text-center dark:divide-white/10">
                    <div class="flex flex-col items-center gap-2 px-4">
                        <span
                            class="flex size-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                        >
                            <x-filament::icon :icon="Heroicon::OutlinedEnvelope" :size="IconSize::Large" />
                        </span>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Enter your email address</p>
                    </div>

                    <div class="flex flex-col items-center gap-2 px-4">
                        <span
                            class="flex size-10 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400"
                        >
                            <x-filament::icon :icon="Heroicon::OutlinedUser" :size="IconSize::Large" />
                        </span>
                        <p class="text-xs text-gray-600 dark:text-gray-400">We identify your account</p>
                    </div>

                    <div class="flex flex-col items-center gap-2 px-4">
                        <span
                            class="flex size-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400"
                        >
                            <x-filament::icon :icon="Heroicon::OutlinedArrowRight" :size="IconSize::Large" />
                        </span>
                        <p class="text-xs text-gray-600 dark:text-gray-400">We guide you through the next step</p>
                    </div>
                </div>

                <div class="mt-8 h-px bg-gray-200 dark:bg-white/10"></div>
            </div>
        @endif

        <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <x-filament::icon
                :icon="Heroicon::ShieldCheck"
                :size="IconSize::Small"
                class="shrink-0 text-gray-400 dark:text-gray-500"
            />
            <span>Your information is secure. We use industry-standard security practices to protect your data.</span>
        </div>
    </section>

    <section
        class="flex flex-col gap-3 rounded-xl bg-white px-6 py-3 shadow-sm ring-1 ring-gray-950/5 sm:px-8 md:flex-row md:items-center md:justify-between dark:bg-gray-900 dark:ring-white/10"
    >
        @if (filled($currentVersion))
            <div class="flex items-center gap-3">
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400"
                >
                    <x-filament::icon :icon="Heroicon::Check" />
                </span>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        Recently updated to {{ Str::start($currentVersion, 'v') }}
                    </p>

                    <x-filament::link
                        :href="$themeChangelogUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        :icon="Heroicon::ArrowRight"
                        icon-position="after"
                        size="sm"
                    >
                        What's new
                    </x-filament::link>
                </div>
            </div>
        @endif

        <div
            class="flex shrink-0 items-center gap-6 border-t border-gray-200 pt-3 md:border-t-0 md:pt-0 dark:border-white/10"
        >
            <div class="hidden h-6 w-px bg-gray-200 md:block dark:bg-white/10"></div>

            <x-filament::link
                :href="$productResourceHubUrl"
                target="_blank"
                rel="noopener noreferrer"
                color="gray"
                :icon="Heroicon::ArrowTopRightOnSquare"
                icon-position="after"
                size="sm"
                class="whitespace-nowrap"
            >
                Knowledge Base &amp; Support
            </x-filament::link>
        </div>
    </section>

    @if ($this->isMobileApp())
        <x-filament::actions class="mt-6" :full-width="true">
            <x-filament::button tag="a" :href="EmailFirstLogin::SWITCH_TENANT_URL" color="gray">
                Switch tenant
            </x-filament::button>
        </x-filament::actions>
    @endif

    <x-filament-actions::modals />
</div>
