<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementInteractionDriver.driver') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="driver"
        >{{ trans('cruds.engagementInteractionDriver.fields.driver') }}</label>
        <input
            class="form-control"
            id="driver"
            name="driver"
            type="text"
            required
            wire:model="engagementInteractionDriver.driver"
        >
        <div class="validation-message">
            {{ $errors->first('engagementInteractionDriver.driver') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionDriver.fields.driver_helper') }}
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
            href="{{ route('admin.engagement-interaction-drivers.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
