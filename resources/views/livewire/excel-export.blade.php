<button
    class="btn btn-secondary disabled:cursor-not-allowed disabled:opacity-50"
    type="button"
    wire:click="export"
    wire:loading.attr="disabled"
>
    <i
        class="fas fa-spinner fa-spin"
        wire:loading
    ></i>
    {{ $format }}
</button>
