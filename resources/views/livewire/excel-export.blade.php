<button wire:click="export" type="button" class="btn btn-secondary disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
    <i wire:loading class="fas fa-spinner fa-spin"></i>
    {{ $format }}
</button>