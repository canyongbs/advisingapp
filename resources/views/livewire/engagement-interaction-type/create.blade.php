<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('engagementInteractionType.type') ? 'invalid' : '' }}">
        <label class="form-label" for="type">{{ trans('cruds.engagementInteractionType.fields.type') }}</label>
        <input class="form-control" type="text" name="type" id="type" wire:model.defer="engagementInteractionType.type">
        <div class="validation-message">
            {{ $errors->first('engagementInteractionType.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionType.fields.type_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-interaction-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>