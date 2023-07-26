<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('kbItemQuality.rating') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="rating"
        >{{ trans('cruds.kbItemQuality.fields.rating') }}</label>
        <input
            class="form-control"
            id="rating"
            name="rating"
            type="text"
            required
            wire:model="kbItemQuality.rating"
        >
        <div class="validation-message">
            {{ $errors->first('kbItemQuality.rating') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItemQuality.fields.rating_helper') }}
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
            href="{{ route('admin.kb-item-qualities.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
