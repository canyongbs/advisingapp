<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('supportPage.title') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="title"
        >{{ trans('cruds.supportPage.fields.title') }}</label>
        <input
            class="form-control"
            id="title"
            name="title"
            type="text"
            required
            wire:model="supportPage.title"
        >
        <div class="validation-message">
            {{ $errors->first('supportPage.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.supportPage.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('supportPage.body') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="body"
        >{{ trans('cruds.supportPage.fields.body') }}</label>
        <textarea
            class="form-control"
            id="body"
            name="body"
            required
            wire:model="supportPage.body"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('supportPage.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.supportPage.fields.body_helper') }}
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
            href="{{ route('admin.support-pages.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
