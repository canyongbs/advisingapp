<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('user.emplid') ? 'invalid' : '' }}">
        <label class="form-label" for="emplid">{{ trans('cruds.user.fields.emplid') }}</label>
        <input class="form-control" type="text" name="emplid" id="emplid" wire:model.defer="user.emplid">
        <div class="validation-message">
            {{ $errors->first('user.emplid') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.emplid_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.user.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="user.name">
        <div class="validation-message">
            {{ $errors->first('user.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.email') ? 'invalid' : '' }}">
        <label class="form-label required" for="email">{{ trans('cruds.user.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" required wire:model.defer="user.email">
        <div class="validation-message">
            {{ $errors->first('user.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.password') ? 'invalid' : '' }}">
        <label class="form-label" for="password">{{ trans('cruds.user.fields.password') }}</label>
        <input class="form-control" type="password" name="password" id="password" wire:model.defer="password">
        <div class="validation-message">
            {{ $errors->first('user.password') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.password_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('roles') ? 'invalid' : '' }}">
        <label class="form-label required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
        <x-select-list class="form-control" required id="roles" name="roles" wire:model="roles" :options="$this->listsForFields['roles']" multiple />
        <div class="validation-message">
            {{ $errors->first('roles') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.roles_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.locale') ? 'invalid' : '' }}">
        <label class="form-label" for="locale">{{ trans('cruds.user.fields.locale') }}</label>
        <input class="form-control" type="text" name="locale" id="locale" wire:model.defer="user.locale">
        <div class="validation-message">
            {{ $errors->first('user.locale') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.locale_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.type') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.user.fields.type') }}</label>
        @foreach($this->listsForFields['type'] as $key => $value)
            <label class="radio-label"><input type="radio" name="type" wire:model="user.type" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('user.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.user.fields.type_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>