@props(['managers'])

@php
    $managers = array_filter($managers, fn(string $manager): bool => $manager::canViewForRecord($this->getRecord(), static::class));
@endphp

@if ($managers)
    <div
        x-data="{ activeTab: @js(array_key_first($managers)) }"
        {{ $attributes->class(['grid gap-3']) }}
    >
        <x-filament::tabs>
            @foreach ($managers as $managerKey => $manager)
                <x-filament::tabs.item :alpine-active="'activeTab === ' . \Illuminate\Support\Js::from($managerKey)" :x-on:click="'activeTab = ' .
                    \Illuminate\Support\Js::from($managerKey)">
                    {{ $manager::getTitle($this->getRecord(), static::class) }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        @foreach ($managers as $managerKey => $manager)
            <div x-show="activeTab === @js($managerKey)">
                @livewire($manager, [
                    'ownerRecord' => $this->getRecord(),
                    'pageClass' => static::class,
                    'lazy' => $loop->first ? false : 'on-load',
                ])
            </div>
        @endforeach
    </div>
@endif
