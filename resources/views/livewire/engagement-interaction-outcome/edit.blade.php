<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementInteractionOutcome.outcome') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="outcome"
        >{{ trans('cruds.engagementInteractionOutcome.fields.outcome') }}</label>
        <input
            class="form-control"
            id="outcome"
            name="outcome"
            type="text"
            required
            wire:model="engagementInteractionOutcome.outcome"
        >
        <div class="validation-message">
            {{ $errors->first('engagementInteractionOutcome.outcome') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionOutcome.fields.outcome_helper') }}
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
            href="{{ route('admin.engagement-interaction-outcomes.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
