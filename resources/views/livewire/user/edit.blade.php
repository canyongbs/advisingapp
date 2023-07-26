<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('user.emplid') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="emplid"
        >{{ trans('cruds.user.fields.emplid') }}</label>
        <input
            class="form-control"
            id="emplid"
            name="emplid"
            type="text"
            wire:model="user.emplid"
        >
        <div class="validation-message">
            {{ $errors->first('user.emplid') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.emplid_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.name') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="name"
        >{{ trans('cruds.user.fields.name') }}</label>
        <input
            class="form-control"
            id="name"
            name="name"
            type="text"
            required
            wire:model="user.name"
        >
        <div class="validation-message">
            {{ $errors->first('user.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.email') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="email"
        >{{ trans('cruds.user.fields.email') }}</label>
        <input
            class="form-control"
            id="email"
            name="email"
            type="email"
            required
            wire:model="user.email"
        >
        <div class="validation-message">
            {{ $errors->first('user.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.password') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="password"
        >{{ trans('cruds.user.fields.password') }}</label>
        <input
            class="form-control"
            id="password"
            name="password"
            type="password"
            wire:model="password"
        >
        <div class="validation-message">
            {{ $errors->first('user.password') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.password_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('roles') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="roles"
        >{{ trans('cruds.user.fields.roles') }}</label>
        <x-select-list
            class="form-control"
            id="roles"
            name="roles"
            required
            wire:model.live="roles"
            :options="$this->listsForFields['roles']"
            multiple
        />
        <div class="validation-message">
            {{ $errors->first('roles') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.roles_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.locale') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="locale"
        >{{ trans('cruds.user.fields.locale') }}</label>
        <input
            class="form-control"
            id="locale"
            name="locale"
            type="text"
            wire:model="user.locale"
        >
        <div class="validation-message">
            {{ $errors->first('user.locale') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.locale_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.type') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.user.fields.type') }}</label>
        @foreach ($this->listsForFields['type'] as $key => $value)
            <label class="radio-label"><input
                    name="type"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="user.type"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('user.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.type_helper') }}
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
            href="{{ route('admin.users.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
