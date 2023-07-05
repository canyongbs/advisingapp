<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('caseItemType.type') ? 'invalid' : '' }}">
        <label class="form-label required" for="type">{{ trans('cruds.caseItemType.fields.type') }}</label>
        <input class="form-control" type="text" name="type" id="type" required wire:model.defer="caseItemType.type">
        <div class="validation-message">
            {{ $errors->first('caseItemType.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.caseItemType.fields.type_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.case-item-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>