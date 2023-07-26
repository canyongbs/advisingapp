<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('caseItem.casenumber') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="casenumber"
        >{{ trans('cruds.caseItem.fields.casenumber') }}</label>
        <input
            class="form-control"
            id="casenumber"
            name="casenumber"
            type="number"
            required
            wire:model="caseItem.casenumber"
            step="1"
        >
        <div class="validation-message">
            {{ $errors->first('caseItem.casenumber') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.casenumber_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.student_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="student"
        >{{ trans('cruds.caseItem.fields.student') }}</label>
        <x-select-list
            class="form-control"
            id="student"
            name="student"
            required
            :options="$this->listsForFields['student']"
            wire:model.live="caseItem.student_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.student_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.institution_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="institution"
        >{{ trans('cruds.caseItem.fields.institution') }}</label>
        <x-select-list
            class="form-control"
            id="institution"
            name="institution"
            required
            :options="$this->listsForFields['institution']"
            wire:model.live="caseItem.institution_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.institution_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.institution_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.state_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="state"
        >{{ trans('cruds.caseItem.fields.state') }}</label>
        <x-select-list
            class="form-control"
            id="state"
            name="state"
            :options="$this->listsForFields['state']"
            wire:model.live="caseItem.state_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.state_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.state_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.type_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="type"
        >{{ trans('cruds.caseItem.fields.type') }}</label>
        <x-select-list
            class="form-control"
            id="type"
            name="type"
            required
            :options="$this->listsForFields['type']"
            wire:model.live="caseItem.type_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.type_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.type_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.priority_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="priority"
        >{{ trans('cruds.caseItem.fields.priority') }}</label>
        <x-select-list
            class="form-control"
            id="priority"
            name="priority"
            required
            :options="$this->listsForFields['priority']"
            wire:model.live="caseItem.priority_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.priority_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.priority_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.assigned_to_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="assigned_to"
        >{{ trans('cruds.caseItem.fields.assigned_to') }}</label>
        <x-select-list
            class="form-control"
            id="assigned_to"
            name="assigned_to"
            :options="$this->listsForFields['assigned_to']"
            wire:model.live="caseItem.assigned_to_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.assigned_to_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.assigned_to_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.close_details') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="close_details"
        >{{ trans('cruds.caseItem.fields.close_details') }}</label>
        <textarea
            class="form-control"
            id="close_details"
            name="close_details"
            wire:model="caseItem.close_details"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('caseItem.close_details') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.close_details_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.res_details') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="res_details"
        >{{ trans('cruds.caseItem.fields.res_details') }}</label>
        <textarea
            class="form-control"
            id="res_details"
            name="res_details"
            wire:model="caseItem.res_details"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('caseItem.res_details') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.res_details_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('caseItem.created_by_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="created_by"
        >{{ trans('cruds.caseItem.fields.created_by') }}</label>
        <x-select-list
            class="form-control"
            id="created_by"
            name="created_by"
            :options="$this->listsForFields['created_by']"
            wire:model.live="caseItem.created_by_id"
        />
        <div class="validation-message">
            {{ $errors->first('caseItem.created_by_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItem.fields.created_by_helper') }}
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
            href="{{ route('admin.case-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
