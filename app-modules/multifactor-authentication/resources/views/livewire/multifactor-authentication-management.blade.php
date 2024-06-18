<div>
    @unless ($user->hasEnabledTwoFactor())
        <h3 class="flex items-center gap-2 text-lg font-medium">
            @svg('heroicon-o-exclamation-circle', 'w-6')
            You have not enabled two factor authentication.
        </h3>
        <p class="text-sm">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your Authenticator application.</p>

        <div class="flex justify-between mt-3">
            {{ $this->enableAction }}
        </div>

    @else
        @if ($user->hasConfirmedTwoFactor())
            <h3 class="flex items-center gap-2 text-lg font-medium">
                @svg('heroicon-o-shield-check', 'w-6')
                You have enabled two factor authentication!
            </h3>
            <p class="text-sm">Two factor authentication is now enabled. This helps make your account more secure.</p>
            <div class="flex justify-between mt-3">
                {{ $this->regenerateCodesAction }}
                {{ $this->disableAction()->color('danger') }}
            </div>
        @else
            <h3 class="flex items-center gap-2 text-lg font-medium">
                @svg('heroicon-o-question-mark-circle', 'w-6')
                Finish enabling two factor authentication.
            </h3>
            <p class="text-sm">To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.</p>
            <div class="flex mt-3 space-x-4">
                <div>
                    {!! $this->getTwoFactorQrCode() !!}
                    <p class="pt-2 text-sm">Setup key {{
                        decrypt($this->user->two_factor_secret) }}</p>
                </div>
            </div>

            <div class="flex justify-between mt-3">
                {{ $this->confirmAction }}
                {{ $this->disableAction }}
            </div>

        @endif

    @endunless
    <x-filament-actions::modals />
</div>