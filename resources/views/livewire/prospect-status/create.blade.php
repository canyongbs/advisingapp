<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('prospectStatus.status') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="status"
        >{{ trans('cruds.prospectStatus.fields.status') }}</label>
        <input
            class="form-control"
            id="status"
            name="status"
            type="text"
            required
            wire:model="prospectStatus.status"
        >
        <div class="validation-message">
            {{ $errors->first('prospectStatus.status') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectStatus.fields.status_helper') }}
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
            href="{{ route('admin.prospect-statuses.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
