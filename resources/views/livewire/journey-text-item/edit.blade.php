<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('journeyTextItem.name') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="name"
        >{{ trans('cruds.journeyTextItem.fields.name') }}</label>
        <input
            class="form-control"
            id="name"
            name="name"
            type="text"
            wire:model="journeyTextItem.name"
        >
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTextItem.text') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="text"
        >{{ trans('cruds.journeyTextItem.fields.text') }}</label>
        <input
            class="form-control"
            id="text"
            name="text"
            type="text"
            wire:model="journeyTextItem.text"
        >
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.text') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.text_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTextItem.start') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="start"
        >{{ trans('cruds.journeyTextItem.fields.start') }}</label>
        <x-date-picker
            class="form-control"
            id="start"
            name="start"
            wire:model.live="journeyTextItem.start"
        />
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.start') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.start_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTextItem.end') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="end"
        >{{ trans('cruds.journeyTextItem.fields.end') }}</label>
        <x-date-picker
            class="form-control"
            id="end"
            name="end"
            wire:model.live="journeyTextItem.end"
        />
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.end') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.end_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTextItem.active') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.journeyTextItem.fields.active') }}</label>
        @foreach ($this->listsForFields['active'] as $key => $value)
            <label class="radio-label"><input
                    name="active"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="journeyTextItem.active"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.active') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.active_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTextItem.frequency') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.journeyTextItem.fields.frequency') }}</label>
        @foreach ($this->listsForFields['frequency'] as $key => $value)
            <label class="radio-label"><input
                    name="frequency"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="journeyTextItem.frequency"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyTextItem.frequency') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTextItem.fields.frequency_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button
            class="btn btn-indigo mr-2"
            type="submit"
        >
            {{ trans('global.save') }}
        </button>
        <a
            class="btn btn-secondary"
            href="{{ route('admin.journey-text-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
