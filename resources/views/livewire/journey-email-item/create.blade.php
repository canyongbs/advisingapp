<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('journeyEmailItem.name') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="name"
        >{{ trans('cruds.journeyEmailItem.fields.name') }}</label>
        <input
            class="form-control"
            id="name"
            name="name"
            type="text"
            required
            wire:model="journeyEmailItem.name"
        >
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.body') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="body"
        >{{ trans('cruds.journeyEmailItem.fields.body') }}</label>
        <textarea
            class="form-control"
            id="body"
            name="body"
            required
            wire:model="journeyEmailItem.body"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.body_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.start') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="start"
        >{{ trans('cruds.journeyEmailItem.fields.start') }}</label>
        <x-date-picker
            class="form-control"
            id="start"
            name="start"
            required
            wire:model.live="journeyEmailItem.start"
        />
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.start') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.start_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.end') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="end"
        >{{ trans('cruds.journeyEmailItem.fields.end') }}</label>
        <x-date-picker
            class="form-control"
            id="end"
            name="end"
            wire:model.live="journeyEmailItem.end"
        />
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.end') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.end_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.active') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.journeyEmailItem.fields.active') }}</label>
        @foreach ($this->listsForFields['active'] as $key => $value)
            <label class="radio-label"><input
                    name="active"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="journeyEmailItem.active"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.active') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.active_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.frequency') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.journeyEmailItem.fields.frequency') }}</label>
        @foreach ($this->listsForFields['frequency'] as $key => $value)
            <label class="radio-label"><input
                    name="frequency"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="journeyEmailItem.frequency"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.frequency') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.frequency_helper') }}
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
            href="{{ route('admin.journey-email-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
