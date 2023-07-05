<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('kbItemQuality.rating') ? 'invalid' : '' }}">
        <label class="form-label required" for="rating">{{ trans('cruds.kbItemQuality.fields.rating') }}</label>
        <input class="form-control" type="text" name="rating" id="rating" required wire:model.defer="kbItemQuality.rating">
        <div class="validation-message">
            {{ $errors->first('kbItemQuality.rating') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItemQuality.fields.rating_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.kb-item-qualities.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>