<div>
    <a
        class="block text-blueGray-500"
        href="#"
        onclick="openDropdown(event,'{{ $this->id }}')"
    >
        <div class="flex items-center">
            <span
                class="inline-flex h-12 w-12 items-center justify-center rounded-full text-sm font-bold uppercase text-pink-400 md:text-white"
            >
                {{ $currentLanguage }}
            </span>
        </div>
    </a>
    <div
        class="z-50 float-left hidden min-w-48 list-none rounded bg-white py-2 text-left text-base shadow-lg"
        id="{{ $this->id }}"
    >
        @foreach ($languages as $language)
            <a
                class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-blueGray-700 hover:text-indigo-600"
                href="#"
                wire:click="changeLocale('{{ $language['short_code'] }}')"
            >
                {{ $language['title'] }}
            </a>
        @endforeach
    </div>
</div>
