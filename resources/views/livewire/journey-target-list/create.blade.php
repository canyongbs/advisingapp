<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('journeyTargetList.name') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="name"
        >{{ trans('cruds.journeyTargetList.fields.name') }}</label>
        <input
            class="form-control"
            id="name"
            name="name"
            type="text"
            wire:model="journeyTargetList.name"
        >
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTargetList.description') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="description"
        >{{ trans('cruds.journeyTargetList.fields.description') }}</label>
        <textarea
            class="form-control"
            id="description"
            name="description"
            wire:model="journeyTargetList.description"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTargetList.query') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="query"
        >{{ trans('cruds.journeyTargetList.fields.query') }}</label>
        <textarea
            class="form-control"
            id="query"
            name="query"
            wire:model="journeyTargetList.query"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.query') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.query_helper') }}
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
            href="{{ route('admin.journey-target-lists.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
