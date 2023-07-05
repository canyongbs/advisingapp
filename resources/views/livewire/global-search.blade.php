<div class="relative flex w-full flex-wrap items-stretch">
    <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
        <i class="fas fa-search"></i>
    </span>

    @if(count($results))
        <span class="cursor-pointer z-10 h-full leading-snug font-normal absolute text-center text-rose-300 absolute bg-transparent rounded right-0 text-base items-center justify-center w-8 pr-3 py-3" wire:click="resetForm">
            <i class="fas fa-times"></i>
        </span>
    @endif

    <input type="text" placeholder="Search here..." wire:model.debounce.300ms="search" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" />

    <div class="absolute rounded w-full shadow-lg mt-12 z-20 bg-white right-0 max-h-96 overflow-y-auto overflow-x-hidden">
        @foreach($results as $group => $entries)
            <div class="text-xs uppercase tracking-wider text-indigo-600 bg-indigo-100 font-bold py-2 px-3">
                {{ $group }}
            </div>

            <ul>
                @foreach($entries as $entry)
                    <li class="cursor-pointer flex items-center hover:bg-blueGray-100 block py-2 px-3 no-underline font-normal">
                        <a href="{{ $entry['linkTo'] }}">
                            @foreach($entry['fields'] as $name => $value)
                                <div class="text-sm text-blueGray-700">{{ $name }}: {{ $value }}</div>
                            @endforeach
                        </a>
                    </li>
                @endforeach
            </ul>

        @endforeach
    </div>
</div>