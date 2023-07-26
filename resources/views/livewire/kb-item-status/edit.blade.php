<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('kbItemStatus.status') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="status"
        >{{ trans('cruds.kbItemStatus.fields.status') }}</label>
        <input
            class="form-control"
            id="status"
            name="status"
            type="text"
            required
            wire:model="kbItemStatus.status"
        >
        <div class="validation-message">
            {{ $errors->first('kbItemStatus.status') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItemStatus.fields.status_helper') }}
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
            href="{{ route('admin.kb-item-statuses.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
