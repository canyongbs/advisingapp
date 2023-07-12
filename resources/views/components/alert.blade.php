<div class="bg-{{ $attributes['variant'] }}-500 relative mb-4 rounded border-0 px-6 py-4 text-white">
    <span class="mr-5 inline-block align-middle text-xl">
        <i class="fas fa-bell"></i>
    </span>
    <span class="mr-8 inline-block align-middle">
        {{ $attributes['message'] }}
    </span>
    <button
        class="absolute right-0 top-0 mr-6 mt-4 bg-transparent text-2xl font-semibold leading-none outline-none focus:outline-none"
        onclick="closeAlert(event)"
    >
        <span>×</span>
    </button>
</div>
