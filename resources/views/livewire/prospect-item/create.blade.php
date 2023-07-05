<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('prospectItem.first') ? 'invalid' : '' }}">
        <label class="form-label required" for="first">{{ trans('cruds.prospectItem.fields.first') }}</label>
        <input class="form-control" type="text" name="first" id="first" required wire:model.defer="prospectItem.first">
        <div class="validation-message">
            {{ $errors->first('prospectItem.first') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.first_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.last') ? 'invalid' : '' }}">
        <label class="form-label required" for="last">{{ trans('cruds.prospectItem.fields.last') }}</label>
        <input class="form-control" type="text" name="last" id="last" required wire:model.defer="prospectItem.last">
        <div class="validation-message">
            {{ $errors->first('prospectItem.last') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.last_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.full') ? 'invalid' : '' }}">
        <label class="form-label required" for="full">{{ trans('cruds.prospectItem.fields.full') }}</label>
        <input class="form-control" type="text" name="full" id="full" required wire:model.defer="prospectItem.full">
        <div class="validation-message">
            {{ $errors->first('prospectItem.full') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.full_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.preferred') ? 'invalid' : '' }}">
        <label class="form-label" for="preferred">{{ trans('cruds.prospectItem.fields.preferred') }}</label>
        <input class="form-control" type="text" name="preferred" id="preferred" wire:model.defer="prospectItem.preferred">
        <div class="validation-message">
            {{ $errors->first('prospectItem.preferred') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.preferred_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.description') ? 'invalid' : '' }}">
        <label class="form-label" for="description">{{ trans('cruds.prospectItem.fields.description') }}</label>
        <textarea class="form-control" name="description" id="description" wire:model.defer="prospectItem.description" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('prospectItem.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ trans('cruds.prospectItem.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" wire:model.defer="prospectItem.email">
        <div class="validation-message">
            {{ $errors->first('prospectItem.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.email_2') ? 'invalid' : '' }}">
        <label class="form-label" for="email_2">{{ trans('cruds.prospectItem.fields.email_2') }}</label>
        <input class="form-control" type="email" name="email_2" id="email_2" wire:model.defer="prospectItem.email_2">
        <div class="validation-message">
            {{ $errors->first('prospectItem.email_2') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.email_2_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.mobile') ? 'invalid' : '' }}">
        <label class="form-label" for="mobile">{{ trans('cruds.prospectItem.fields.mobile') }}</label>
        <input class="form-control" type="number" name="mobile" id="mobile" wire:model.defer="prospectItem.mobile" step="1">
        <div class="validation-message">
            {{ $errors->first('prospectItem.mobile') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.mobile_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.sms_opt_out') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.prospectItem.fields.sms_opt_out') }}</label>
        @foreach($this->listsForFields['sms_opt_out'] as $key => $value)
            <label class="radio-label"><input type="radio" name="sms_opt_out" wire:model="prospectItem.sms_opt_out" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('prospectItem.sms_opt_out') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.sms_opt_out_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.email_bounce') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.prospectItem.fields.email_bounce') }}</label>
        @foreach($this->listsForFields['email_bounce'] as $key => $value)
            <label class="radio-label"><input type="radio" name="email_bounce" wire:model="prospectItem.email_bounce" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('prospectItem.email_bounce') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.email_bounce_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.status_id') ? 'invalid' : '' }}">
        <label class="form-label" for="status">{{ trans('cruds.prospectItem.fields.status') }}</label>
        <x-select-list class="form-control" id="status" name="status" :options="$this->listsForFields['status']" wire:model="prospectItem.status_id" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.status_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.status_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.source_id') ? 'invalid' : '' }}">
        <label class="form-label" for="source">{{ trans('cruds.prospectItem.fields.source') }}</label>
        <x-select-list class="form-control" id="source" name="source" :options="$this->listsForFields['source']" wire:model="prospectItem.source_id" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.source_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.source_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.phone') ? 'invalid' : '' }}">
        <label class="form-label" for="phone">{{ trans('cruds.prospectItem.fields.phone') }}</label>
        <input class="form-control" type="number" name="phone" id="phone" wire:model.defer="prospectItem.phone" step="1">
        <div class="validation-message">
            {{ $errors->first('prospectItem.phone') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.phone_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.address') ? 'invalid' : '' }}">
        <label class="form-label" for="address">{{ trans('cruds.prospectItem.fields.address') }}</label>
        <input class="form-control" type="text" name="address" id="address" wire:model.defer="prospectItem.address">
        <div class="validation-message">
            {{ $errors->first('prospectItem.address') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.address_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.address_2') ? 'invalid' : '' }}">
        <label class="form-label" for="address_2">{{ trans('cruds.prospectItem.fields.address_2') }}</label>
        <input class="form-control" type="text" name="address_2" id="address_2" wire:model.defer="prospectItem.address_2">
        <div class="validation-message">
            {{ $errors->first('prospectItem.address_2') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.address_2_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.birthdate') ? 'invalid' : '' }}">
        <label class="form-label" for="birthdate">{{ trans('cruds.prospectItem.fields.birthdate') }}</label>
        <x-date-picker class="form-control" wire:model="prospectItem.birthdate" id="birthdate" name="birthdate" picker="date" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.birthdate') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.birthdate_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.hsgrad') ? 'invalid' : '' }}">
        <label class="form-label" for="hsgrad">{{ trans('cruds.prospectItem.fields.hsgrad') }}</label>
        <input class="form-control" type="text" name="hsgrad" id="hsgrad" wire:model.defer="prospectItem.hsgrad">
        <div class="validation-message">
            {{ $errors->first('prospectItem.hsgrad') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.hsgrad_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.hsdate') ? 'invalid' : '' }}">
        <label class="form-label" for="hsdate">{{ trans('cruds.prospectItem.fields.hsdate') }}</label>
        <x-date-picker class="form-control" wire:model="prospectItem.hsdate" id="hsdate" name="hsdate" picker="date" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.hsdate') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.hsdate_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.assigned_to_id') ? 'invalid' : '' }}">
        <label class="form-label" for="assigned_to">{{ trans('cruds.prospectItem.fields.assigned_to') }}</label>
        <x-select-list class="form-control" id="assigned_to" name="assigned_to" :options="$this->listsForFields['assigned_to']" wire:model="prospectItem.assigned_to_id" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.assigned_to_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.assigned_to_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('prospectItem.created_by_id') ? 'invalid' : '' }}">
        <label class="form-label" for="created_by">{{ trans('cruds.prospectItem.fields.created_by') }}</label>
        <x-select-list class="form-control" id="created_by" name="created_by" :options="$this->listsForFields['created_by']" wire:model="prospectItem.created_by_id" />
        <div class="validation-message">
            {{ $errors->first('prospectItem.created_by_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectItem.fields.created_by_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.prospect-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>