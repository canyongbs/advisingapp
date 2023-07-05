<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('prospectStatus.status') ? 'invalid' : '' }}">
        <label class="form-label required" for="status">{{ trans('cruds.prospectStatus.fields.status') }}</label>
        <input class="form-control" type="text" name="status" id="status" required wire:model.defer="prospectStatus.status">
        <div class="validation-message">
            {{ $errors->first('prospectStatus.status') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectStatus.fields.status_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.prospect-statuses.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>