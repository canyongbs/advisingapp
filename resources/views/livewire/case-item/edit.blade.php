<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('caseItem.casenumber') ? 'invalid' : '' }}">
        <label class="form-label required" for="casenumber">{{ trans('cruds.caseItem.fields.casenumber') }}</label>
        <input class="form-control" type="number" name="casenumber" id="casenumber" required wire:model.defer="caseItem.casenumber" step="1">
        <div class="validation-message">
            {{ $errors->first('caseItem.casenumber') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.casenumber_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.student_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="student">{{ trans('cruds.caseItem.fields.student') }}</label>
        <x-select-list class="form-control" required id="student" name="student" :options="$this->listsForFields['student']" wire:model="caseItem.student_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.student_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.institution_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="institution">{{ trans('cruds.caseItem.fields.institution') }}</label>
        <x-select-list class="form-control" required id="institution" name="institution" :options="$this->listsForFields['institution']" wire:model="caseItem.institution_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.institution_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.institution_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.state_id') ? 'invalid' : '' }}">
        <label class="form-label" for="state">{{ trans('cruds.caseItem.fields.state') }}</label>
        <x-select-list class="form-control" id="state" name="state" :options="$this->listsForFields['state']" wire:model="caseItem.state_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.state_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.state_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.type_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="type">{{ trans('cruds.caseItem.fields.type') }}</label>
        <x-select-list class="form-control" required id="type" name="type" :options="$this->listsForFields['type']" wire:model="caseItem.type_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.type_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.type_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.priority_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="priority">{{ trans('cruds.caseItem.fields.priority') }}</label>
        <x-select-list class="form-control" required id="priority" name="priority" :options="$this->listsForFields['priority']" wire:model="caseItem.priority_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.priority_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.priority_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.assigned_to_id') ? 'invalid' : '' }}">
        <label class="form-label" for="assigned_to">{{ trans('cruds.caseItem.fields.assigned_to') }}</label>
        <x-select-list class="form-control" id="assigned_to" name="assigned_to" :options="$this->listsForFields['assigned_to']" wire:model="caseItem.assigned_to_id" />
        <div class="validation-message">
            {{ $errors->first('caseItem.assigned_to_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.assigned_to_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.close_details') ? 'invalid' : '' }}">
        <label class="form-label" for="close_details">{{ trans('cruds.caseItem.fields.close_details') }}</label>
        <textarea class="form-control" name="close_details" id="close_details" wire:model.defer="caseItem.close_details" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('caseItem.close_details') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.close_details_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.res_details') ? 'invalid' : '' }}">
        <label class="form-label" for="res_details">{{ trans('cruds.caseItem.fields.res_details') }}</label>
        <textarea class="form-control" name="res_details" id="res_details" wire:model.defer="caseItem.res_details" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('caseItem.res_details') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.res_details_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.case-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>