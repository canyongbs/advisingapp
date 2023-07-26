<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementTextItem.mobile') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="mobile"
        >{{ trans('cruds.engagementTextItem.fields.mobile') }}</label>
        <input
            class="form-control"
            id="mobile"
            name="mobile"
            type="number"
            required
            wire:model="engagementTextItem.mobile"
            step="1"
        >
        <div class="validation-message">
            {{ $errors->first('engagementTextItem.mobile') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementTextItem.fields.mobile_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementTextItem.message') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="message"
        >{{ trans('cruds.engagementTextItem.fields.message') }}</label>
        <input
            class="form-control"
            id="message"
            name="message"
            type="text"
            wire:model="engagementTextItem.message"
        >
        <div class="validation-message">
            {{ $errors->first('engagementTextItem.message') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementTextItem.fields.message_helper') }}
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
            href="{{ route('admin.engagement-text-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
