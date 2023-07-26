<div wire:ignore>
    <div class="flatpickr flatpickr-{{ $attributes['id'] }} relative">
        @if (!isset($attributes['required']))
            <div class="absolute inset-y-0 left-0 flex items-center">
                <button
                    class="h-full w-10 text-rose-600"
                    id="clear-{{ $attributes['id'] }}"
                    data-clear
                    type="button"
                >
                    <i class="far fa-times-circle"></i>
                </button>
            </div>
        @endif

        <input
            class="form-control"
            data-input
            type="text"
            {{ $attributes }}
        >
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("livewire:init", () => {
            function update(value) {
                let el = document.getElementById('clear-{{ $attributes['id'] }}')

                if (value === '') {
                    value = null

                    if (el !== null) {
                        el.classList.add('invisible')
                    }
                } else if (el !== null) {
                    el.classList.remove('invisible')
                }

                @this.set('{{ $attributes['wire:model.live'] }}', value)
            }

            @if ($attributes['picker'] === 'date')
                let el = flatpickr('.flatpickr-{{ $attributes['id'] }}', {
                    dateFormat: "{{ config('project.flatpickr_date_format') }}",
                    wrap: true,
                    onChange: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    },
                    onReady: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    }
                })
            @elseif ($attributes['picker'] === 'time')
                let el = flatpickr('.flatpickr-{{ $attributes['id'] }}', {
                    enableTime: true,
                    // enableSeconds: true,
                    noCalendar: true,
                    time_24hr: true,
                    wrap: true,
                    dateFormat: "{{ config('project.flatpickr_time_format') }}",
                    onChange: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    },
                    onReady: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    }
                })
            @else
                let el = flatpickr('.flatpickr-{{ $attributes['id'] }}', {
                    enableTime: true,
                    time_24hr: true,
                    wrap: true,
                    // enableSeconds: true,
                    dateFormat: "{{ config('project.flatpickr_datetime_format') }}",
                    onChange: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    },
                    onReady: (SelectedDates, DateStr, instance) => {
                        update(DateStr)
                    }
                })
            @endif
        });
    </script>
@endpush
