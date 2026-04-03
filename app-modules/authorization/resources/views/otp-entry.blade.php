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

<x-layout>
    <section>
        <div class="mx-auto max-w-screen-xl px-4 py-8 lg:px-6 lg:py-16">
            <div class="mx-auto max-w-md">
                <div class="mb-6 text-center">
                    <h1 class="mb-2 text-3xl font-extrabold text-gray-900 dark:text-white">Enter your OTP</h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Enter the 6-digit code that was provided to you. The code expires in 20 minutes.
                    </p>
                </div>

                @if ($errors->any())
                    <div
                        class="mb-4 rounded-lg border border-danger-200 bg-danger-50 p-4 dark:border-danger-700 dark:bg-danger-900/20"
                    >
                        <p class="text-sm text-danger-700 dark:text-danger-400">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ $verifyUrl }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="block text-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            OTP Code
                        </label>
                        <div class="flex justify-center gap-3" id="otp-inputs">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="1"
                                    pattern="\d"
                                    autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                                    {{ $i === 0 ? 'autofocus' : '' }}
                                    class="otp-digit h-14 w-12 rounded-lg border border-gray-300 bg-white text-center text-2xl font-semibold text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                            @endfor
                        </div>
                        <input type="hidden" name="code" id="otp-hidden" />
                    </div>

                    <x-filament::button type="submit" class="w-full" size="lg">Verify &amp; Sign In</x-filament::button>
                </form>
            </div>
        </div>
    </section>

    <script>
        const digits = Array.from(document.querySelectorAll('.otp-digit'));
        const hidden = document.getElementById('otp-hidden');
        const form = document.querySelector('form');

        digits.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '').slice(-1);
                if (input.value && index < digits.length - 1) {
                    digits[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((char, i) => {
                    if (digits[i]) digits[i].value = char;
                });
                const next = digits[Math.min(pasted.length, digits.length - 1)];
                if (next) next.focus();
            });
        });

        form.addEventListener('submit', () => {
            hidden.value = digits.map((d) => d.value).join('');
        });
    </script>
</x-layout>
