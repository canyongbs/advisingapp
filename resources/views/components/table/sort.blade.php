@if (in_array($field, $orderable))
    @if ($sortBy !== $field)
        <i
            class="fa fa-fw fa-sort cursor-pointer"
            aria-hidden="true"
            wire:click="sortBy('{{ $field }}')"
        ></i>
    @elseif($sortBy === $field && $sortDirection == 'desc')
        <i
            class="fa fa-fw fa-sort-down cursor-pointer text-blue-500"
            aria-hidden="true"
            wire:click="sortBy('{{ $field }}')"
        ></i>
    @else
        <i
            class="fa fa-fw fa-sort-up cursor-pointer text-blue-500"
            aria-hidden="true"
            wire:click="sortBy('{{ $field }}')"
        ></i>
    @endif
@endif
