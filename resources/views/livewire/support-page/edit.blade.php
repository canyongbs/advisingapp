<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('supportPage.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.supportPage.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="supportPage.title">
        <div class="validation-message">
            {{ $errors->first('supportPage.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.supportPage.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('supportPage.body') ? 'invalid' : '' }}">
        <label class="form-label required" for="body">{{ trans('cruds.supportPage.fields.body') }}</label>
        <textarea class="form-control" name="body" id="body" required wire:model.defer="supportPage.body" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('supportPage.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.supportPage.fields.body_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.support-pages.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>