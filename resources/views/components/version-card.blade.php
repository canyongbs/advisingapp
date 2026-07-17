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

@props([
    'themeChangelogUrl',
])

<div
    {{ $attributes->class(['@container flex flex-col rounded-xl bg-turkish-500 p-6 text-white shadow-sm ring-1 ring-turkish-500/50']) }}
>
    <div class="@sm:flex-row @sm:items-center flex flex-1 flex-col items-start justify-between gap-x-4 gap-y-6">
        <div class="flex flex-1 flex-col items-start @sm:self-stretch">
            <p class="text-sm font-semibold text-white">Version {{ app('current-version') }} is now available!</p>
            <p class="mt-2 mb-4 text-sm text-white">
                Your instance of Advising App&#174; was automatically updated with our latest available features.
            </p>
            <a
                class="mt-auto inline-flex items-center gap-1.5 rounded-lg border-2 border-white px-4 py-2 text-sm font-semibold text-white transition duration-75 hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-turkish-500 focus-visible:outline-none"
                href="{{ $themeChangelogUrl }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                Learn More
            </a>
        </div>
        <img
            class="@sm:order-last order-first block max-h-28"
            src="{{ Vite::asset('resources/images/version.svg') }}"
            alt="Drawing of a man touching a floating card displaying information"
        />
    </div>
</div>
