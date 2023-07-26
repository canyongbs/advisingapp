<form
    class="pt-3"
    wire:submit="submit"
>

    <div class="form-group {{ $errors->has('mediaCollections.engagement_student_file_file') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="file"
        >{{ trans('cruds.engagementStudentFile.fields.file') }}</label>
        <x-dropzone
            id="file"
            name="file"
            action="{{ route('admin.engagement-student-files.storeMedia') }}"
            collection-name="engagement_student_file_file"
            max-file-size="2"
        />
        <div class="validation-message">
            {{ $errors->first('mediaCollections.engagement_student_file_file') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.file_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementStudentFile.description') ? 'invalid' : '' }}">
        <label
            class="form-label"
            for="description"
        >{{ trans('cruds.engagementStudentFile.fields.description') }}</label>
        <input
            class="form-control"
            id="description"
            name="description"
            type="text"
            wire:model="engagementStudentFile.description"
        >
        <div class="validation-message">
            {{ $errors->first('engagementStudentFile.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementStudentFile.student_id') ? 'invalid' : '' }}">
        <label
            class="form-label required"
            for="student"
        >{{ trans('cruds.engagementStudentFile.fields.student') }}</label>
        <x-select-list
            class="form-control"
            id="student"
            name="student"
            required
            :options="$this->listsForFields['student']"
            wire:model.live="engagementStudentFile.student_id"
        />
        <div class="validation-message">
            {{ $errors->first('engagementStudentFile.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.student_helper') }}
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
            href="{{ route('admin.engagement-student-files.index') }}"
        >
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
