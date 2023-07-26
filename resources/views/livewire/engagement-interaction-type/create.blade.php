<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementInteractionType.type') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="type"
        >{{ trans('cruds.engagementInteractionType.fields.type') }}</label>
        <input
            class="form-control"
            id="type"
            name="type"
            type="text"
            wire:model="engagementInteractionType.type"
        >
        <div class="validation-message">
            {{ $errors->first('engagementInteractionType.type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementInteractionType.fields.type_helper') }}
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
            href="{{ route('admin.engagement-interaction-types.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
