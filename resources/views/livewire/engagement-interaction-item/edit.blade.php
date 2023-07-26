<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementInteractionItem.direction') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.engagementInteractionItem.fields.direction') }}</label>
        @foreach ($this->listsForFields['direction'] as $key => $value)
            <label class="radio-label"><input
                    name="direction"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="engagementInteractionItem.direction"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('engagementInteractionItem.direction') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionItem.fields.direction_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementInteractionItem.start') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="start"
        >{{ trans('cruds.engagementInteractionItem.fields.start') }}</label>
        <x-date-picker
            class="form-control"
            id="start"
            name="start"
            required
            wire:model.live="engagementInteractionItem.start"
        />
        <div class="validation-message">
            {{ $errors->first('engagementInteractionItem.start') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionItem.fields.start_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementInteractionItem.duration') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.engagementInteractionItem.fields.duration') }}</label>
        @foreach ($this->listsForFields['duration'] as $key => $value)
            <label class="radio-label"><input
                    name="duration"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="engagementInteractionItem.duration"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('engagementInteractionItem.duration') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionItem.fields.duration_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementInteractionItem.subject') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="subject"
        >{{ trans('cruds.engagementInteractionItem.fields.subject') }}</label>
        <input
            class="form-control"
            id="subject"
            name="subject"
            type="text"
            required
            wire:model="engagementInteractionItem.subject"
        >
        <div class="validation-message">
            {{ $errors->first('engagementInteractionItem.subject') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionItem.fields.subject_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementInteractionItem.description') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="description"
        >{{ trans('cruds.engagementInteractionItem.fields.description') }}</label>
        <textarea
            class="form-control"
            id="description"
            name="description"
            wire:model="engagementInteractionItem.description"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('engagementInteractionItem.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionItem.fields.description_helper') }}
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
            href="{{ route('admin.engagement-interaction-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
