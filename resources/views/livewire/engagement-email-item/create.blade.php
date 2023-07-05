<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('engagementEmailItem.email') ? 'invalid' : '' }}">
        <label class="form-label required" for="email">{{ trans('cruds.engagementEmailItem.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" required wire:model.defer="engagementEmailItem.email">
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementEmailItem.subject') ? 'invalid' : '' }}">
        <label class="form-label required" for="subject">{{ trans('cruds.engagementEmailItem.fields.subject') }}</label>
        <input class="form-control" type="text" name="subject" id="subject" required wire:model.defer="engagementEmailItem.subject">
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.subject') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.subject_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementEmailItem.body') ? 'invalid' : '' }}">
        <label class="form-label required" for="body">{{ trans('cruds.engagementEmailItem.fields.body') }}</label>
        <textarea class="form-control" name="body" id="body" required wire:model.defer="engagementEmailItem.body" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.body_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-email-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>