<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('engagementInteractionOutcome.outcome') ? 'invalid' : '' }}">
        <label class="form-label required" for="outcome">{{ trans('cruds.engagementInteractionOutcome.fields.outcome') }}</label>
        <input class="form-control" type="text" name="outcome" id="outcome" required wire:model.defer="engagementInteractionOutcome.outcome">
        <div class="validation-message">
            {{ $errors->first('engagementInteractionOutcome.outcome') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionOutcome.fields.outcome_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-interaction-outcomes.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>