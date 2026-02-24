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
<div>
    @if (! auth()->user()->is_external)
        @unless ($user->hasEnabledMultifactor())
            <h3 class="flex items-center gap-2 text-lg font-medium">
                @svg('heroicon-o-exclamation-circle', 'w-6')
                You have not enabled multifactor authentication.
            </h3>
            <p class="text-sm">
                When multifactor authentication is enabled, you will be prompted for a secure, random token during
                authentication. You may retrieve this token from your Authenticator application.
            </p>

            <div class="mt-3 flex justify-between">
                {{ $this->enableAction }}
            </div>
        @else
            @if ($user->hasConfirmedMultifactor())
                <h3 class="flex items-center gap-2 text-lg font-medium">
                    @svg('heroicon-o-shield-check', 'w-6')
                    You have enabled multifactor authentication!
                </h3>
                <p class="text-sm">
                    Multifactor authentication is now enabled. This helps make your account more secure.
                </p>
                <div class="mt-3 flex justify-between">
                    {{ $this->regenerateCodesAction }}
                    {{ $this->disableAction()->color('danger') }}
                </div>
            @else
                <h3 class="flex items-center gap-2 text-lg font-medium">
                    @svg('heroicon-o-question-mark-circle', 'w-6')
                    Finish enabling multifactor authentication.
                </h3>
                <p class="text-sm">
                    To finish enabling multifactor authentication, scan the following QR code using your phone's
                    authenticator application or enter the setup key and provide the generated OTP code.
                </p>
                <div class="mt-3 flex space-x-4">
                    <div>
                        {!! $this->getMultifactorQrCode() !!}
                        <p class="pt-2 text-sm">Setup key {{ decrypt($this->user->multifactor_secret) }}</p>
                    </div>
                </div>

                <div class="mt-3 flex justify-between">
                    {{ $this->confirmAction }}
                    {{ $this->disableAction }}
                </div>
            @endif
        @endunless
        <x-filament-actions::modals />
    @else
        {{ $this->form }}
    @endif
</div>
