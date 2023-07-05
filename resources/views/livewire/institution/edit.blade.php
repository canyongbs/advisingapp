<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('institution.code') ? 'invalid' : '' }}">
        <label class="form-label" for="code">{{ trans('cruds.institution.fields.code') }}</label>
        <input class="form-control" type="text" name="code" id="code" wire:model.defer="institution.code">
        <div class="validation-message">
            {{ $errors->first('institution.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('institution.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.institution.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="institution.name">
        <div class="validation-message">
            {{ $errors->first('institution.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('institution.description') ? 'invalid' : '' }}">
        <label class="form-label" for="description">{{ trans('cruds.institution.fields.description') }}</label>
        <textarea class="form-control" name="description" id="description" wire:model.defer="institution.description" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('institution.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.description_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>