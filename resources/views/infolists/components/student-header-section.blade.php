<div {{ $attributes }}>
    <div class="flex sm:flex-row flex-col w-full items-start justify-start md:gap-8 gap-4">
        @if ($getNameWords())
        <div class="">
            <span class="text-2xl text-white flex h-16 w-16 items-center justify-center rounded-full  bg-blue-500">{{ $getNameWords() }}</span>
        </div>
        @endif
        <div class="gap-4">
            <h2 class="mb-3 text-3xl font-semibold text-black">{{ $getState()?->full_name }}</h2>
            <div class="mb-3 flex items-center lg:gap-6 gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-user" class="w-5"></x-icon>
                    <span class="font-medium">Student</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-heart" class="w-5"></x-icon>
                    <span class="font-medium">Goes by "{{ $getState()?->preferred }}"</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-phone" class="w-5"></x-icon>
                    <span class="font-medium">{{ $getState()?->phone }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-envelope" class="w-5"></x-icon>
                    <span class="font-medium">{{ $getState()?->email }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="heroicon-m-building-library" class="w-5"></x-icon>
                    <span class="font-medium">{{ $getState()?->hsgrad }}</span>
                </div>
            </div>
            <div class="flex items-center gap-6">
                @if ($getState()?->firstgen)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    First Gen
                </span>
                @endif
                @if ($getState()?->dual)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    Dual
                </span>
                @endif
                @if ($getState()?->sap)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    SAP
                </span>
                @endif
                @if ($getState()?->dfw)
                <span class="border border-blue-500 px-6 py-1 text-sm text-blue-500 font-medium">
                    DFW {{ $getState()?->dfw }}
                </span>
                @endif
            </div>
        </div>
    </div>
    {{ $getChildComponentContainer() }}
</div>
