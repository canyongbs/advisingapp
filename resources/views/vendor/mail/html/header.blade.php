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
@props(['url', 'settings' => null])
@php
    use App\Models\SettingsProperty;
    use AdvisingApp\Theme\Settings\ThemeSettings;

    $themeSettings = app(ThemeSettings::class);
    $logo = $themeSettings->getSettingsPropertyModel('theme.is_logo_active')->getFirstMedia('logo');
@endphp
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if ($settings?->hasMedia('logo'))
                {{-- TODO: Don't use temporary urls? --}}
                <img src="{{ $settings?->getFirstTemporaryUrl(now()->addDays(6), 'logo') }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="Logo">
            @elseif ($themeSettings->is_logo_active && $logo)
                <img src="{{ $logo->getTemporaryUrl(now()->addDays(6)) }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="Logo">
            @else
                <img src="{{ url(Vite::asset('resources/images/default-logo-light-201124.svg')) }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="Logo">
            @endif
        </a>
    </td>
</tr>
