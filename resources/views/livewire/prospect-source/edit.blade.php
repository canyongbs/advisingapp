<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('prospectSource.source') ? 'invalid' : '' }}">
        <label class="form-label required" for="source">{{ trans('cruds.prospectSource.fields.source') }}</label>
        <input class="form-control" type="text" name="source" id="source" required wire:model.defer="prospectSource.source">
        <div class="validation-message">
            {{ $errors->first('prospectSource.source') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectSource.fields.source_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.prospect-sources.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>