<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('journeyEmailItem.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.journeyEmailItem.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="journeyEmailItem.name">
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.body') ? 'invalid' : '' }}">
        <label class="form-label required" for="body">{{ trans('cruds.journeyEmailItem.fields.body') }}</label>
        <textarea class="form-control" name="body" id="body" required wire:model.defer="journeyEmailItem.body" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.body_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.start') ? 'invalid' : '' }}">
        <label class="form-label required" for="start">{{ trans('cruds.journeyEmailItem.fields.start') }}</label>
        <x-date-picker class="form-control" required wire:model="journeyEmailItem.start" id="start" name="start" />
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.start') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.start_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.end') ? 'invalid' : '' }}">
        <label class="form-label" for="end">{{ trans('cruds.journeyEmailItem.fields.end') }}</label>
        <x-date-picker class="form-control" wire:model="journeyEmailItem.end" id="end" name="end" />
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.end') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.end_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.active') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.journeyEmailItem.fields.active') }}</label>
        @foreach($this->listsForFields['active'] as $key => $value)
            <label class="radio-label"><input type="radio" name="active" wire:model="journeyEmailItem.active" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.active') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.active_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyEmailItem.frequency') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.journeyEmailItem.fields.frequency') }}</label>
        @foreach($this->listsForFields['frequency'] as $key => $value)
            <label class="radio-label"><input type="radio" name="frequency" wire:model="journeyEmailItem.frequency" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyEmailItem.frequency') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyEmailItem.fields.frequency_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.journey-email-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>