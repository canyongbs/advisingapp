<div class="relative flex w-full flex-wrap items-stretch">
    <span
        class="absolute absolute z-10 h-full w-8 items-center justify-center rounded bg-transparent py-3 pl-3 text-center text-base font-normal leading-snug text-blueGray-300"
    >
        <i class="fas fa-search"></i>
    </span>

    @if (count($results))
        <span
            class="absolute absolute right-0 z-10 h-full w-8 cursor-pointer items-center justify-center rounded bg-transparent py-3 pr-3 text-center text-base font-normal leading-snug text-rose-300"
            wire:click="resetForm"
        >
            <i class="fas fa-times"></i>
        </span>
    @endif

    <input
        class="relative w-full rounded border-0 bg-white bg-white px-3 py-3 pl-10 text-sm text-blueGray-600 placeholder-blueGray-300 shadow outline-none focus:outline-none focus:ring"
        type="text"
        placeholder="Search here..."
        wire:model.live.debounce.300ms="search"
    />

    <div
        class="absolute right-0 z-20 mt-12 max-h-96 w-full overflow-y-auto overflow-x-hidden rounded bg-white shadow-lg">
        @foreach ($results as $group => $entries)
            <div class="bg-indigo-100 px-3 py-2 text-xs font-bold uppercase tracking-wider text-indigo-600">
                {{ $group }}
            </div>

            <ul>
                @foreach ($entries as $entry)
                    <li
                        class="block flex cursor-pointer items-center px-3 py-2 font-normal no-underline hover:bg-blueGray-100">
                        <a href="{{ $entry['linkTo'] }}">
                            @foreach ($entry['fields'] as $name => $value)
                                <div class="text-sm text-blueGray-700">{{ $name }}: {{ $value }}</div>
                            @endforeach
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>
