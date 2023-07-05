<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('userAlert.message') ? 'invalid' : '' }}">
        <label class="form-label required" for="message">{{ trans('cruds.userAlert.fields.message') }}</label>
        <input class="form-control" type="text" name="message" id="message" required wire:model.defer="userAlert.message">
        <div class="validation-message">
            {{ $errors->first('userAlert.message') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.message_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('userAlert.link') ? 'invalid' : '' }}">
        <label class="form-label" for="link">{{ trans('cruds.userAlert.fields.link') }}</label>
        <input class="form-control" type="text" name="link" id="link" wire:model.defer="userAlert.link">
        <div class="validation-message">
            {{ $errors->first('userAlert.link') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.link_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('users') ? 'invalid' : '' }}">
        <label class="form-label required" for="users">{{ trans('cruds.userAlert.fields.users') }}</label>
        <x-select-list class="form-control" required id="users" name="users" wire:model="users" :options="$this->listsForFields['users']" multiple />
        <div class="validation-message">
            {{ $errors->first('users') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.userAlert.fields.users_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.user-alerts.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>