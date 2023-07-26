<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('userAlert.message') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="message"
        >{{ trans('cruds.userAlert.fields.message') }}</label>
        <input
            class="form-control"
            id="message"
            name="message"
            type="text"
            required
            wire:model="userAlert.message"
        >
        <div class="validation-message">
            {{ $errors->first('userAlert.message') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.message_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('userAlert.link') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="link"
        >{{ trans('cruds.userAlert.fields.link') }}</label>
        <input
            class="form-control"
            id="link"
            name="link"
            type="text"
            wire:model="userAlert.link"
        >
        <div class="validation-message">
            {{ $errors->first('userAlert.link') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.link_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('users') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="users"
        >{{ trans('cruds.userAlert.fields.users') }}</label>
        <x-select-list
            class="form-control"
            id="users"
            name="users"
            required
            wire:model.live="users"
            :options="$this->listsForFields['users']"
            multiple
        />
        <div class="validation-message">
            {{ $errors->first('users') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.users_helper') }}
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
            href="{{ route('admin.user-alerts.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
