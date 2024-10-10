<div class="flex sm:flex-row flex-col w-full items-start justify-start md:gap-8 gap-4">
        @if ($this->getNameWords())
        <div class="">
            <span class="text-2xl text-white flex h-16 w-16 items-center justify-center rounded-full  bg-blue-500">{{ $this->getNameWords() }}</span>
        </div>
        @endif
        <div class="gap-4">
            <h2 class="mb-3 text-3xl font-semibold text-black">{{ $record?->full_name }}</h2>
            <div class="mb-3 flex items-center lg:gap-6 gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-user" class="w-5"></x-icon>
                    <span class="font-medium">Student</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-heart" class="w-5"></x-icon>
                    <span class="font-medium">Goes by "{{ $record?->preferred }}"</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-phone" class="w-5"></x-icon>
                    <span class="font-medium">{{ $record?->phone }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-envelope" class="w-5"></x-icon>
                    <span class="font-medium">{{ $record?->email }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-building-library" class="w-5"></x-icon>
                    <span class="font-medium">{{ $record?->hsgrad }}</span>
                </div>
            </div>
            <div class="flex items-center gap-6">
                @if ($record?->firstgen)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    First Gen
                </span>
                @endif
                @if ($record?->dual)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    Dual
                </span>
                @endif
                @if ($record?->sap)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    SAP
                </span>
                @endif
                @if ($record?->dfw)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    DFW {{ $record?->dfw }}
                </span>
                @endif
            </div>
        </div>
    </div>
