<div>
    <div wire:ignore class="w-full">
        @if(isset($attributes['multiple']))
            <div id="{{ $attributes['id'] }}-btn-container" class="mb-3">
                <button type="button" class="btn btn-info btn-sm select-all-button">{{ trans('global.select_all') }}</button>
                <button type="button" class="btn btn-info btn-sm deselect-all-button">{{ trans('global.deselect_all') }}</button>
            </div>
        @endif
        <select class="select2 form-control" data-placeholder="{{ __('Select your option') }}" {{ $attributes }}>
            @if(!isset($attributes['multiple']))
                <option></option>
            @endif
            @foreach($options as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("livewire:load", () => {
    let el = $('#{{ $attributes['id'] }}')
    let buttonsId = '#{{ $attributes['id'] }}-btn-container'

    function initButtons() {
        $(buttonsId + ' .select-all-button').click(function (e) {
            el.val(_.map(el.find('option'), opt => $(opt).attr('value')))
            el.trigger('change')
        })

        $(buttonsId + ' .deselect-all-button').click(function (e) {
            el.val([])
            el.trigger('change')
        })
    }

    function initSelect () {
        initButtons()
        el.select2({
            placeholder: '{{ __('Select your option') }}',
            allowClear: !el.attr('required')
        })
    }

    initSelect()

    Livewire.hook('message.processed', (message, component) => {
        initSelect()
    });

    el.on('change', function (e) {
        let data = $(this).select2("val")
        if (data === "") {
            data = null
        }
@this.set('{{ $attributes['wire:model'] }}', data)
    });
});
    </script>
@endpush