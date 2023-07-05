<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('engagementInteractionRelation.relation') ? 'invalid' : '' }}">
        <label class="form-label required" for="relation">{{ trans('cruds.engagementInteractionRelation.fields.relation') }}</label>
        <input class="form-control" type="text" name="relation" id="relation" required wire:model.defer="engagementInteractionRelation.relation">
        <div class="validation-message">
            {{ $errors->first('engagementInteractionRelation.relation') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionRelation.fields.relation_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-interaction-relations.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>