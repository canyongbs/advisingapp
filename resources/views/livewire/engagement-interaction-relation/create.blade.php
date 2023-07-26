<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementInteractionRelation.relation') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="relation"
        >{{ trans('cruds.engagementInteractionRelation.fields.relation') }}</label>
        <input
            class="form-control"
            id="relation"
            name="relation"
            type="text"
            required
            wire:model="engagementInteractionRelation.relation"
        >
        <div class="validation-message">
            {{ $errors->first('engagementInteractionRelation.relation') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionRelation.fields.relation_helper') }}
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
            href="{{ route('admin.engagement-interaction-relations.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
