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
    use AdvisingApp\Portal\Settings\PortalSettings;
@endphp

<div class="js-cookie-consent cookie-consent z-50 fixed bottom-0 inset-x-0 pb-2">
    <div class="max-w-7xl mx-auto px-6">
        <div class="p-2 rounded-lg bg-primary-100 ring-1 ring-black/5 shadow-sm">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 items-center hidden md:inline [&_a]:text-primary-500 hover:[&_a]:underline">
                    <p class="ml-3 text-primary-950 text-sm font-medium cookie-consent__message">
                        @if(! empty(app(PortalSettings::class)->gdpr_banner_text))
                            {!! str(tiptap_converter()->asHtml(app(PortalSettings::class)->gdpr_banner_text))->sanitizeHtml() !!}
                        @else
                          We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you've provided to them or that they've collected from your use of their services.
                        @endif
                    </p>
                </div>

                <div class="mt-2 flex-shrink-0 w-full sm:mt-0 sm:w-auto">
                    <x-filament::button class="js-cookie-consent-agree cookie-consent__agree">
                        @if(! empty(app(PortalSettings::class)->gdpr_banner_button_label))
                            {{ str(app(PortalSettings::class)->gdpr_banner_button_label->getLabel()) }}
                        @else
                            Allow Cookies
                        @endif
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</div>
