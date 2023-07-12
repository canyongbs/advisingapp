<nav
    class="absolute left-0 top-0 z-10 flex w-full items-center bg-transparent p-4 md:flex-row md:flex-nowrap md:justify-start">
    <div class="mx-auto flex w-full flex-wrap items-center justify-between px-4 md:flex-nowrap md:px-10">
        <a
            class="hidden text-sm font-semibold uppercase text-white lg:inline-block"
            href="#"
        >
            {{-- Dashboard --}}
        </a>

        {{-- If you use user icon and menu add margin mr-3 to search --}}
        {{-- <form class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3"> --}}
        <form class="hidden flex-row flex-wrap items-center md:flex lg:ml-auto">
            @livewire('global-search')
        </form>

        <ul class="hidden list-none flex-wrap items-center md:flex">
            <li class="relative inline-block">
                <a
                    class="block cursor-pointer px-3 py-1 text-white"
                    onclick="openDropdown(event,'nav-notification-dropdown')"
                >
                    <i class="fas fa-bell"></i>
                    @if ($new_alert_count = auth()->user()->alerts()->wherePivot('seen_at', null)->count())
                        <span
                            class="min-w-5 absolute -top-1 inline-flex h-5 justify-center rounded-full bg-indigo-600 text-xs font-semibold leading-5 text-white"
                        >
                            <span class="px-1">{{ $new_alert_count }}</span>
                        </span>
                    @endif
                </a>
                <div
                    class="z-50 float-left hidden min-w-48 list-none rounded bg-white py-2 text-left text-base shadow-lg"
                    id="nav-notification-dropdown"
                    data-popper-placement="bottom-start"
                    style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(617px, 58px);"
                >
                    @forelse(auth()->user()->alerts()->latest()->take(10)->get() as $alert)
                        @if ($alert->link)
                            <a
                                class="{{ $alert->pivot->seen_at ? 'text-blueGray-400' : 'text-blueGray-700' }} block w-full cursor-pointer whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal hover:bg-blueGray-100"
                                href="{{ $alert->link }}"
                                target="_blank"
                            >
                                <i class="fas fa-link fa-fw mr-1"></i>
                                {{ $alert->message }}
                            </a>
                        @else
                            <a
                                class="{{ $alert->pivot->seen_at ? 'text-blueGray-400' : 'text-blueGray-700' }} block w-full cursor-pointer whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal hover:bg-blueGray-100">
                                <i class="fas fa-bell fa-fw mr-1"></i>
                                {{ $alert->message }}
                            </a>
                        @endif
                    @empty
                        {{ __('global.no_alerts') }}
                    @endforelse
                </div>
            </li>
        </ul>

        @if (file_exists(app_path('Http/Livewire/LanguageSwitcher.php')))
            <ul class="hidden list-none flex-col items-center md:flex md:flex-row">
                <livewire:language-switcher />
            </ul>
        @endif

        {{-- User icon and menu --}}
        {{--
        <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
            <a class="text-blueGray-500 block" href="#pablo" onclick="openDropdown(event,'user-dropdown')">
                <div class="items-center flex">
                    <span class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full"><img alt="..." class="w-full rounded-full align-middle border-none shadow-lg" src="https://demos.creative-tim.com/notus-js/assets/img/team-1-800x800.jpg" /></span>
                </div>
            </a>
            <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="user-dropdown">
                <a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Action</a><a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Another action</a><a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Something else here</a>
                <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
                <a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Seprated link</a>
            </div>
        </ul>
         --}}
    </div>
</nav>
