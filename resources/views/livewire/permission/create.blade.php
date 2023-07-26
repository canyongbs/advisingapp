<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('permission.title') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="title"
        >{{ trans('cruds.permission.fields.title') }}</label>
        <input
            class="form-control"
            id="title"
            name="title"
            type="text"
            required
            wire:model="permission.title"
        >
        <div class="validation-message">
            {{ $errors->first('permission.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.permission.fields.title_helper') }}
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
            href="{{ route('admin.permissions.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
