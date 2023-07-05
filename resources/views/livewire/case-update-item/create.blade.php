<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('caseUpdateItem.student_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="student">{{ trans('cruds.caseUpdateItem.fields.student') }}</label>
        <x-select-list class="form-control" required id="student" name="student" :options="$this->listsForFields['student']" wire:model="caseUpdateItem.student_id" />
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.student_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.case_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="case">{{ trans('cruds.caseUpdateItem.fields.case') }}</label>
        <x-select-list class="form-control" required id="case" name="case" :options="$this->listsForFields['case']" wire:model="caseUpdateItem.case_id" />
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.case_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.case_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.update') ? 'invalid' : '' }}">
        <label class="form-label required" for="update">{{ trans('cruds.caseUpdateItem.fields.update') }}</label>
        <input class="form-control" type="text" name="update" id="update" required wire:model.defer="caseUpdateItem.update">
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.update') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.update_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.internal') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.caseUpdateItem.fields.internal') }}</label>
        @foreach($this->listsForFields['internal'] as $key => $value)
            <label class="radio-label"><input type="radio" name="internal" wire:model="caseUpdateItem.internal" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.internal') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.internal_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.direction') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.caseUpdateItem.fields.direction') }}</label>
        @foreach($this->listsForFields['direction'] as $key => $value)
            <label class="radio-label"><input type="radio" name="direction" wire:model="caseUpdateItem.direction" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.direction') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.direction_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.case-update-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>