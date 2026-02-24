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
    use App\Settings\DisplaySettings;

    $displaySettings = app(DisplaySettings::class);
@endphp

<div class="flex items-center justify-center px-4 py-8 sm:py-16">
    <div class="w-full max-w-4xl">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            @if ($this->heroImageUrl)
                <div class="relative h-48 w-full overflow-hidden sm:h-64 md:h-80">
                    <img
                        class="h-full w-full object-contain"
                        src="{{ $this->heroImageUrl }}"
                        alt="{{ $event->title }}"
                    />
                </div>
            @endif

            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                            {{ $event->title }}
                        </h1>
                        @if ($this->createdByName)
                            <p class="mt-1 text-sm text-gray-600">By {{ $this->createdByName }}</p>
                        @endif
                    </div>
                    @if ($this->heroImageUrl)
                        <button
                            class="inline-flex items-center justify-center rounded-md bg-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                            wire:click="openRegistrationModal"
                        >
                            Register
                        </button>
                    @endif
                </div>

                @if ($event->location || ($event->starts_at && $event->ends_at))
                    <div class="mt-6 space-y-2 text-sm text-gray-600">
                        @if ($event->location)
                            <p>{{ $event->location }}</p>
                        @endif

                        @if ($event->starts_at && $event->ends_at)
                            <p>
                                {{ $event->starts_at->setTimezone($displaySettings->timezone ?: config('app.timezone', 'UTC'))->format('M d, Y') }}
                                from
                                {{ $event->starts_at->setTimezone($displaySettings->timezone ?: config('app.timezone', 'UTC'))->format('g:i a') }}
                                to
                                {{ $event->ends_at->setTimezone($displaySettings->timezone ?: config('app.timezone', 'UTC'))->format('g:i a T') }}
                            </p>
                        @endif
                    </div>
                @endif

                @if ($this->descriptionHtml)
                    <hr class="my-6 border-gray-200" />
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Event Description</h2>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $this->descriptionHtml !!}
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    <button
                        class="inline-flex items-center justify-center rounded-md bg-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                        wire:click="openRegistrationModal"
                    >
                        Register
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($showRegistrationModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-labelledby="modal-title"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true"
                wire:click="closeRegistrationModal"
            ></div>

            <div
                class="relative flex w-full max-w-[95vw] flex-col overflow-hidden rounded-lg bg-white shadow-xl sm:max-w-[90vw] md:max-w-[85vw] lg:max-w-5xl"
                style="max-height: 90vh"
            >
                <button
                    class="absolute right-4 top-4 z-10 rounded-full bg-white p-1 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    wire:click="closeRegistrationModal"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto">
                    @if ($this->event->eventRegistrationForm)
                        <iframe
                            class="h-full w-full border-0"
                            src="{{ route('event-registration.form-modal', ['event' => $this->event]) }}"
                            title="Event Registration Form"
                            style="min-height: calc(90vh - 3rem)"
                        ></iframe>
                    @else
                        <div class="p-4 py-12 text-center sm:p-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                                <svg
                                    class="h-6 w-6 text-yellow-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                    />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">Registration Form Not Available</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                The registration form for this event has not been configured yet.
                            </p>
                            <div class="mt-6">
                                <button
                                    class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                                    wire:click="closeRegistrationModal"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
