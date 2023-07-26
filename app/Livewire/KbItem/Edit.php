<?php

namespace App\Livewire\KbItem;

use App\Models\KbItem;
use Livewire\Component;
use App\Models\Institution;
use App\Models\KbItemStatus;
use App\Models\KbItemQuality;
use App\Models\KbItemCategory;

class Edit extends Component
{
    public KbItem $kbItem;

    public array $institution = [];

    public array $listsForFields = [];

    public function mount(KbItem $kbItem)
    {
        $this->kbItem = $kbItem;
        $this->institution = $this->kbItem->institution()->pluck('id')->toArray();
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.kb-item.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->kbItem->save();
        $this->kbItem->institution()->sync($this->institution);

        return redirect()->route('admin.kb-items.index');
    }

    protected function rules(): array
    {
        return [
            'kbItem.question' => [
                'string',
                'required',
            ],
            'kbItem.quality_id' => [
                'integer',
                'exists:kb_item_qualities,id',
                'nullable',
            ],
            'kbItem.status_id' => [
                'integer',
                'exists:kb_item_statuses,id',
                'nullable',
            ],
            'kbItem.public' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['public'])),
            ],
            'kbItem.category_id' => [
                'integer',
                'exists:kb_item_categories,id',
                'nullable',
            ],
            'institution' => [
                'array',
            ],
            'institution.*.id' => [
                'integer',
                'exists:institutions,id',
            ],
            'kbItem.solution' => [
                'string',
                'nullable',
            ],
            'kbItem.notes' => [
                'string',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['quality'] = KbItemQuality::pluck('rating', 'id')->toArray();
        $this->listsForFields['status'] = KbItemStatus::pluck('status', 'id')->toArray();
        $this->listsForFields['public'] = $this->kbItem::PUBLIC_RADIO;
        $this->listsForFields['category'] = KbItemCategory::pluck('category', 'id')->toArray();
        $this->listsForFields['institution'] = Institution::pluck('name', 'id')->toArray();
    }
}
