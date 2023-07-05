<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('permission.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.permission.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="permission.title">
        <div class="validation-message">
            {{ $errors->first('permission.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.permission.fields.title_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>