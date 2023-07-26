<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('role.title') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="title"
        >{{ trans('cruds.role.fields.title') }}</label>
        <input
            class="form-control"
            id="title"
            name="title"
            type="text"
            required
            wire:model="role.title"
        >
        <div class="validation-message">
            {{ $errors->first('role.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.role.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('permissions') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="permissions"
        >{{ trans('cruds.role.fields.permissions') }}</label>
        <x-select-list
            class="form-control"
            id="permissions"
            name="permissions"
            required
            wire:model.live="permissions"
            :options="$this->listsForFields['permissions']"
            multiple
        />
        <div class="validation-message">
            {{ $errors->first('permissions') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.role.fields.permissions_helper') }}
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
            href="{{ route('admin.roles.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
