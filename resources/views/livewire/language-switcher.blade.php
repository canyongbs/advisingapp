<div>
    <a class="text-blueGray-500 block" href="#" onclick="openDropdown(event,'{{ $this->id }}')">
        <div class="items-center flex">
            <span class="w-12 h-12 text-sm text-pink-400 md:text-white inline-flex items-center justify-center rounded-full font-bold uppercase">
                {{ $currentLanguage }}
            </span>
        </div>
    </a>
    <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="{{ $this->id }}">
        @foreach($languages as $language)
            <a wire:click="changeLocale('{{ $language['short_code'] }}')" href="#" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700 hover:text-indigo-600">
                {{ $language['title'] }}
            </a>
        @endforeach
    </div>
</div>