<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('prospectSource.source') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="source"
        >{{ trans('cruds.prospectSource.fields.source') }}</label>
        <input
            class="form-control"
            id="source"
            name="source"
            type="text"
            required
            wire:model="prospectSource.source"
        >
        <div class="validation-message">
            {{ $errors->first('prospectSource.source') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.prospectSource.fields.source_helper') }}
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
            href="{{ route('admin.prospect-sources.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
