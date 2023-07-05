<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('journeyItem.name') ? 'invalid' : '' }}">
        <label class="form-label" for="name">{{ trans('cruds.journeyItem.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" wire:model.defer="journeyItem.name">
        <div class="validation-message">
            {{ $errors->first('journeyItem.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyItem.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyItem.body') ? 'invalid' : '' }}">
        <label class="form-label required" for="body">{{ trans('cruds.journeyItem.fields.body') }}</label>
        <textarea class="form-control" name="body" id="body" required wire:model.defer="journeyItem.body" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyItem.body') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyItem.fields.body_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyItem.start') ? 'invalid' : '' }}">
        <label class="form-label required" for="start">{{ trans('cruds.journeyItem.fields.start') }}</label>
        <x-date-picker class="form-control" required wire:model="journeyItem.start" id="start" name="start" />
        <div class="validation-message">
            {{ $errors->first('journeyItem.start') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyItem.fields.start_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyItem.end') ? 'invalid' : '' }}">
        <label class="form-label required" for="end">{{ trans('cruds.journeyItem.fields.end') }}</label>
        <x-date-picker class="form-control" required wire:model="journeyItem.end" id="end" name="end" />
        <div class="validation-message">
            {{ $errors->first('journeyItem.end') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyItem.fields.end_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyItem.frequency') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.journeyItem.fields.frequency') }}</label>
        @foreach($this->listsForFields['frequency'] as $key => $value)
            <label class="radio-label"><input type="radio" name="frequency" wire:model="journeyItem.frequency" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('journeyItem.frequency') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyItem.fields.frequency_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.journey-items.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>