<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('kbItemCategory.category') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="category"
        >{{ trans('cruds.kbItemCategory.fields.category') }}</label>
        <input
            class="form-control"
            id="category"
            name="category"
            type="text"
            required
            wire:model="kbItemCategory.category"
        >
        <div class="validation-message">
            {{ $errors->first('kbItemCategory.category') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItemCategory.fields.category_helper') }}
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
            href="{{ route('admin.kb-item-categories.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
