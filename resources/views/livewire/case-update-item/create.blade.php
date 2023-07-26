<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('caseUpdateItem.student_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="student"
        >{{ trans('cruds.caseUpdateItem.fields.student') }}</label>
        <x-select-list
            class="form-control"
            id="student"
            name="student"
            required
            :options="$this->listsForFields['student']"
            wire:model.live="caseUpdateItem.student_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.student_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.case_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="case"
        >{{ trans('cruds.caseUpdateItem.fields.case') }}</label>
        <x-select-list
            class="form-control"
            id="case"
            name="case"
            required
            :options="$this->listsForFields['case']"
            wire:model.live="caseUpdateItem.case_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.case_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.case_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.update') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="update"
        >{{ trans('cruds.caseUpdateItem.fields.update') }}</label>
        <input
            class="form-control"
            id="update"
            name="update"
            type="text"
            required
            wire:model="caseUpdateItem.update"
        >
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.update') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.update_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseUpdateItem.internal') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.caseUpdateItem.fields.internal') }}</label>
        @foreach ($this->listsForFields['internal'] as $key => $value)
            <label class="radio-label"><input
                    name="internal"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="caseUpdateItem.internal"
                >{{ $value }}</label>
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
        @foreach ($this->listsForFields['direction'] as $key => $value)
            <label class="radio-label"><input
                    name="direction"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="caseUpdateItem.direction"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('caseUpdateItem.direction') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseUpdateItem.fields.direction_helper') }}
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
            href="{{ route('admin.case-update-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
