<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('mediaCollections.engagement_student_file_file') ? 'invalid' : '' }}">
        <label class="form-label required" for="file">{{ trans('cruds.engagementStudentFile.fields.file') }}</label>
        <x-dropzone id="file" name="file" action="{{ route('admin.engagement-student-files.storeMedia') }}" collection-name="engagement_student_file_file" max-file-size="2" />
        <div class="validation-message">
            {{ $errors->first('mediaCollections.engagement_student_file_file') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.file_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementStudentFile.description') ? 'invalid' : '' }}">
        <label class="form-label" for="description">{{ trans('cruds.engagementStudentFile.fields.description') }}</label>
        <input class="form-control" type="text" name="description" id="description" wire:model.defer="engagementStudentFile.description">
        <div class="validation-message">
            {{ $errors->first('engagementStudentFile.description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('engagementStudentFile.student_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="student">{{ trans('cruds.engagementStudentFile.fields.student') }}</label>
        <x-select-list class="form-control" required id="student" name="student" :options="$this->listsForFields['student']" wire:model="engagementStudentFile.student_id" />
        <div class="validation-message">
            {{ $errors->first('engagementStudentFile.student_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.engagementStudentFile.fields.student_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.engagement-student-files.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>