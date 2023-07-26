<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('caseItemType.type') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="type"
        >{{ trans('cruds.caseItemType.fields.type') }}</label>
        <input
            class="form-control"
            id="type"
            name="type"
            type="text"
            required
            wire:model="caseItemType.type"
        >
        <div class="validation-message">
            {{ $errors->first('caseItemType.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItemType.fields.type_helper') }}
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
            href="{{ route('admin.case-item-types.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
