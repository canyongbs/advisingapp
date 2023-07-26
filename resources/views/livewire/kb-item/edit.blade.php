<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('kbItem.question') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="question"
        >{{ trans('cruds.kbItem.fields.question') }}</label>
        <input
            class="form-control"
            id="question"
            name="question"
            type="text"
            required
            wire:model="kbItem.question"
        >
        <div class="validation-message">
            {{ $errors->first('kbItem.question') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.question_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.quality_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="quality"
        >{{ trans('cruds.kbItem.fields.quality') }}</label>
        <x-select-list
            class="form-control"
            id="quality"
            name="quality"
            :options="$this->listsForFields['quality']"
            wire:model.live="kbItem.quality_id"
        />
        <div class="validation-message">
            {{ $errors->first('kbItem.quality_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.quality_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.status_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="status"
        >{{ trans('cruds.kbItem.fields.status') }}</label>
        <x-select-list
            class="form-control"
            id="status"
            name="status"
            :options="$this->listsForFields['status']"
            wire:model.live="kbItem.status_id"
        />
        <div class="validation-message">
            {{ $errors->first('kbItem.status_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.status_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.public') ? 'invalid' : '' }}">
        <label class="form-label required">{{ trans('cruds.kbItem.fields.public') }}</label>
        @foreach ($this->listsForFields['public'] as $key => $value)
            <label class="radio-label"><input
                    name="public"
                    type="radio"
                    value="{{ $key }}"
                    wire:model.live="kbItem.public"
                >{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('kbItem.public') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.public_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.category_id') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="category"
        >{{ trans('cruds.kbItem.fields.category') }}</label>
        <x-select-list
            class="form-control"
            id="category"
            name="category"
            :options="$this->listsForFields['category']"
            wire:model.live="kbItem.category_id"
        />
        <div class="validation-message">
            {{ $errors->first('kbItem.category_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.category_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('institution') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="institution"
        >{{ trans('cruds.kbItem.fields.institution') }}</label>
        <x-select-list
            class="form-control"
            id="institution"
            name="institution"
            wire:model.live="institution"
            :options="$this->listsForFields['institution']"
            multiple
        />
        <div class="validation-message">
            {{ $errors->first('institution') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.institution_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.solution') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="solution"
        >{{ trans('cruds.kbItem.fields.solution') }}</label>
        <textarea
            class="form-control"
            id="solution"
            name="solution"
            wire:model="kbItem.solution"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('kbItem.solution') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.solution_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('kbItem.notes') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="notes"
        >{{ trans('cruds.kbItem.fields.notes') }}</label>
        <textarea
            class="form-control"
            id="notes"
            name="notes"
            wire:model="kbItem.notes"
            rows="4"
        ></textarea>
        <div class="validation-message">
            {{ $errors->first('kbItem.notes') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.kbItem.fields.notes_helper') }}
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
            href="{{ route('admin.kb-items.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
