<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('engagementEmailItem.email') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="email"
        >{{ trans('cruds.engagementEmailItem.fields.email') }}</label>
        <input
            class="form-control"
            id="email"
            name="email"
            type="email"
            required
            wire:model="engagementEmailItem.email"
        >
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementEmailItem.subject') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="subject"
        >{{ trans('cruds.engagementEmailItem.fields.subject') }}</label>
        <input
            class="form-control"
            id="subject"
            name="subject"
            type="text"
            required
            wire:model="engagementEmailItem.subject"
        >
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.subject') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.subject_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementEmailItem.body') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="body"
        >{{ trans('cruds.engagementEmailItem.fields.body') }}</label>
        <textarea
            class="form-control"
            id="body"
            name="body"
            required
            wire:model="engagementEmailItem.body"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('engagementEmailItem.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementEmailItem.fields.body_helper') }}
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
            href="{{ route('admin.engagement-email-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
