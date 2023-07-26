<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('caseItemPriority.priority') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="priority"
        >{{ trans('cruds.caseItemPriority.fields.priority') }}</label>
        <input
            class="form-control"
            id="priority"
            name="priority"
            type="text"
            required
            wire:model="caseItemPriority.priority"
        >
        <div class="validation-message">
            {{ $errors->first('caseItemPriority.priority') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItemPriority.fields.priority_helper') }}
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
            href="{{ route('admin.case-item-priorities.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
