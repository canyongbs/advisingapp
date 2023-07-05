<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('engagementInteractionDriver.driver') ? 'invalid' : '' }}">
        <label class="form-label required" for="driver">{{ trans('cruds.engagementInteractionDriver.fields.driver') }}</label>
        <input class="form-control" type="text" name="driver" id="driver" required wire:model.defer="engagementInteractionDriver.driver">
        <div class="validation-message">
            {{ $errors->first('engagementInteractionDriver.driver') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionDriver.fields.driver_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-interaction-drivers.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>