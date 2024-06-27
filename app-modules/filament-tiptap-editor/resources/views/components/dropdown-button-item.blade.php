@props([
    'action' => null,
])

<li {{ $attributes->except('action') }}>
    <button
        class="block w-full whitespace-nowrap px-3 py-2 text-left hover:bg-primary-500 focus:bg-primary-500"
        type="button"
        x-on:click="{{ $action }}; $refs.panel.close();"
    >
        {{ $slot }}
    </button>
</li>
