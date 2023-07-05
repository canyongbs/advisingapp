<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('journeyTargetList.name') ? 'invalid' : '' }}">
        <label class="form-label" for="name">{{ trans('cruds.journeyTargetList.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" wire:model.defer="journeyTargetList.name">
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTargetList.description') ? 'invalid' : '' }}">
        <label class="form-label" for="description">{{ trans('cruds.journeyTargetList.fields.description') }}</label>
        <textarea class="form-control" name="description" id="description" wire:model.defer="journeyTargetList.description" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('journeyTargetList.query') ? 'invalid' : '' }}">
        <label class="form-label" for="query">{{ trans('cruds.journeyTargetList.fields.query') }}</label>
        <textarea class="form-control" name="query" id="query" wire:model.defer="journeyTargetList.query" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('journeyTargetList.query') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.journeyTargetList.fields.query_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.journey-target-lists.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>