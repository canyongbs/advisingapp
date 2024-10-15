{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    use App\Settings\CollegeBrandingSettings;
    use Filament\Support\Enums\MaxWidth;
    use App\Features\EnableBrandingBar;

    $navigation = filament()->getNavigation();
    $collegeBrandingSettings = app(CollegeBrandingSettings::class);
    $currentUser = auth()->user();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @if (filament()->hasTopbar())
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

        <x-filament-panels::topbar :navigation="$navigation" />

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_AFTER, scopes: $livewire->getRenderHookScopes()) }}
        @if ($collegeBrandingSettings->is_enabled && ! EnableBrandingBar::active())
            <div
                style="--c-600: {{ \Filament\Support\Colors\Color::all()[$collegeBrandingSettings->color][600] }}"
                class="sticky top-16 z-10 bg-custom-600 text-sm font-medium text-white px-6 py-2 flex items-center h-10"
            >
                {{ $collegeBrandingSettings->college_text }}
            </div>
        @elseif ($collegeBrandingSettings->is_enabled && EnableBrandingBar::active())
            <livewire:branding-bar />
        @endif

        {{-- The sidebar is after the page content in the markup to fix issues with page content overlapping dropdown content from the sidebar. --}}
        <div
            class="fi-layout flex min-h-[calc(100dvh-6.5rem)] w-full flex-row-reverse overflow-x-clip"
        >
            <div
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-data="{}"
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; opacity:1;'"
                {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
                @elseif (filament()->isSidebarFullyCollapsibleOnDesktop())
                    x-data="{}"
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; opacity:1;'"
                {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
                @elseif (! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation() || (! filament()->hasNavigation())))
                    x-data="{}"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
                @endif
                @class([
                    'fi-main-ctn w-screen flex-1 flex-col',
                    'h-full opacity-0 transition-all' => filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop(),
                    'opacity-0' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation() || (! filament()->hasNavigation())),
                    'flex' => filament()->hasTopNavigation() || (! filament()->hasNavigation()),
                ])
            >
                <main
                    @class([
                        'fi-main mx-auto h-full w-full px-4 md:px-6 lg:px-8',
                        match ($maxContentWidth ??= (filament()->getMaxContentWidth() ?? MaxWidth::SevenExtraLarge)) {
                            MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
                            MaxWidth::Small, 'sm' => 'max-w-sm',
                            MaxWidth::Medium, 'md' => 'max-w-md',
                            MaxWidth::Large, 'lg' => 'max-w-lg',
                            MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
                            MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
                            MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                            MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
                            MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
                            MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
                            MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
                            MaxWidth::Full, 'full' => 'max-w-full',
                            MaxWidth::MinContent, 'min' => 'max-w-min',
                            MaxWidth::MaxContent, 'max' => 'max-w-max',
                            MaxWidth::FitContent, 'fit' => 'max-w-fit',
                            MaxWidth::Prose, 'prose' => 'max-w-prose',
                            MaxWidth::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',
                            MaxWidth::ScreenMedium, 'screen-md' => 'max-w-screen-md',
                            MaxWidth::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',
                            MaxWidth::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',
                            MaxWidth::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',
                            default => $maxContentWidth,
                        },
                    ])
                >
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START, scopes: $livewire->getRenderHookScopes()) }}

                    {{ $slot }}

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $livewire->getRenderHookScopes()) }}
                </main>

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $livewire->getRenderHookScopes()) }}
            </div>

            @if (filament()->hasNavigation())
                <div
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.close()"
                    x-show="$store.sidebar.isOpen"
                    x-transition.opacity.300ms
                    class="fi-sidebar-close-overlay fixed inset-0 z-30 bg-gray-950/50 transition duration-500 dark:bg-gray-950/75 lg:hidden"
                ></div>

                <x-filament-panels::sidebar
                    :navigation="$navigation"
                    :has-branding-bar="EnableBrandingBar::active() ? $collegeBrandingSettings->is_enabled && ! $currentUser->is_branding_bar_dismissed : $collegeBrandingSettings->is_enabled"
                    class="fi-main-sidebar"
                />

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        setTimeout(() => {
                            let activeSidebarItem = document.querySelector(
                                '.fi-main-sidebar .fi-sidebar-item.fi-active',
                            )

                            if (
                                !activeSidebarItem ||
                                activeSidebarItem.offsetParent === null
                            ) {
                                activeSidebarItem = document.querySelector(
                                    '.fi-main-sidebar .fi-sidebar-group.fi-active',
                                )
                            }

                            if (
                                !activeSidebarItem ||
                                activeSidebarItem.offsetParent === null
                            ) {
                                return
                            }

                            const sidebarWrapper = document.querySelector(
                                '.fi-main-sidebar .fi-sidebar-nav',
                            )

                            if (!sidebarWrapper) {
                                return
                            }

                            sidebarWrapper.scrollTo(
                                0,
                                activeSidebarItem.offsetTop -
                                window.innerHeight / 2,
                            )
                        }, 10)
                    })
                </script>
            @endif
        </div>
    @endif
</x-filament-panels::layout.base>
