<?php

namespace App\Livewire\EngagementStudentFile;

use Livewire\Component;
use App\Models\RecordStudentItem;
use App\Models\EngagementStudentFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Edit extends Component
{
    public array $mediaToRemove = [];

    public array $listsForFields = [];

    public array $mediaCollections = [];

    public EngagementStudentFile $engagementStudentFile;

    public function addMedia($media): void
    {
        $this->mediaCollections[$media['collection_name']][] = $media;
    }

    public function removeMedia($media): void
    {
        $collection = collect($this->mediaCollections[$media['collection_name']]);

        $this->mediaCollections[$media['collection_name']] = $collection->reject(fn ($item) => $item['uuid'] === $media['uuid'])->toArray();

        $this->mediaToRemove[] = $media['uuid'];
    }

    public function getMediaCollection($name)
    {
        return $this->mediaCollections[$name];
    }

    public function mount(EngagementStudentFile $engagementStudentFile)
    {
        $this->engagementStudentFile = $engagementStudentFile;
        $this->initListsForFields();
        $this->mediaCollections = [
            'engagement_student_file_file' => $engagementStudentFile->file,
        ];
    }

    public function render()
    {
        return view('livewire.engagement-student-file.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementStudentFile->save();
        $this->syncMedia();

        return redirect()->route('admin.engagement-student-files.index');
    }

    protected function syncMedia(): void
    {
        collect($this->mediaCollections)->flatten(1)
            ->each(fn ($item) => Media::where('uuid', $item['uuid'])
                ->update(['model_id' => $this->engagementStudentFile->id]));

        Media::whereIn('uuid', $this->mediaToRemove)->delete();
    }

    protected function rules(): array
    {
        return [
            'mediaCollections.engagement_student_file_file' => [
                'array',
                'required',
            ],
            'mediaCollections.engagement_student_file_file.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'engagementStudentFile.description' => [
                'string',
                'nullable',
            ],
            'engagementStudentFile.student_id' => [
                'integer',
                'exists:record_student_items,id',
                'required',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['student'] = RecordStudentItem::pluck('full', 'id')->toArray();
    }
}
