<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('caseItemPriority.priority') ? 'invalid' : '' }}">
        <label class="form-label required" for="priority">{{ trans('cruds.caseItemPriority.fields.priority') }}</label>
        <input class="form-control" type="text" name="priority" id="priority" required wire:model.defer="caseItemPriority.priority">
        <div class="validation-message">
            {{ $errors->first('caseItemPriority.priority') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItemPriority.fields.priority_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.case-item-priorities.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>