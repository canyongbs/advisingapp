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

<div class="flex w-full flex-col items-center justify-center gap-8 lg:flex-row">
    <div class="w-full lg:w-1/2 lg:pr-8">
        <form class="grid gap-y-6" wire:submit="authenticate">
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

            <x-filament::actions :actions="$this->getFormActions()" :full-width="$this->hasFullWidthFormActions()" />
            @if (count($this->getSsoFormActions()) > 0)
                <small class="text-gray-800 dark:text-gray-300">or log in with single sign-on</small>
            @endif

            <x-filament::actions
                :actions="$this->getSsoFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </form>

        @if ($this->needsMFA && ! $this->needsMfaSetup)
            <x-filament::link
                class="cursor-pointer"
                size="sm"
                wire:click.prevent="toggleUsingRecoveryCodes()"
                tag="button"
            >
                @if ($this->usingRecoveryCode)
                    Use MFA Code
                @else
                    Use Recovery Code
                @endif
            </x-filament::link>
        @endif
    </div>

    <div class="flex w-full flex-col gap-6 lg:w-1/2">
        <x-authorization::login-version-card :themeChangelogUrl="$themeChangelogUrl" />
        <x-authorization::login-resource-portal-card :productResourcehubUrl="$productResourcehubUrl" />
    </div>
</div>
