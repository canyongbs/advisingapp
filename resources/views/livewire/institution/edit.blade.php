<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('institution.code') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="code"
        >{{ trans('cruds.institution.fields.code') }}</label>
        <input
            class="form-control"
            id="code"
            name="code"
            type="text"
            wire:model="institution.code"
        >
        <div class="validation-message">
            {{ $errors->first('institution.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('institution.name') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="name"
        >{{ trans('cruds.institution.fields.name') }}</label>
        <input
            class="form-control"
            id="name"
            name="name"
            type="text"
            required
            wire:model="institution.name"
        >
        <div class="validation-message">
            {{ $errors->first('institution.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('institution.description') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="description"
        >{{ trans('cruds.institution.fields.description') }}</label>
        <textarea
            class="form-control"
            id="description"
            name="description"
            wire:model="institution.description"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('institution.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.institution.fields.description_helper') }}
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
            href="{{ route('admin.institutions.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
