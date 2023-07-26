<?php

namespace App\Livewire\EngagementInteractionItem;

use Livewire\Component;
use App\Models\EngagementInteractionItem;

class Create extends Component
{
    public array $listsForFields = [];

    public EngagementInteractionItem $engagementInteractionItem;

    public function mount(EngagementInteractionItem $engagementInteractionItem)
    {
        $this->engagementInteractionItem = $engagementInteractionItem;
        $this->engagementInteractionItem->direction = 'inbound';
        $this->engagementInteractionItem->duration = '15';
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.engagement-interaction-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementInteractionItem->save();

        return redirect()->route('admin.engagement-interaction-items.index');
    }

    protected function rules(): array
    {
        return [
            'engagementInteractionItem.direction' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['direction'])),
            ],
            'engagementInteractionItem.start' => [
                'required',
                'date_format:' . config('project.datetime_format'),
            ],
            'engagementInteractionItem.duration' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['duration'])),
            ],
            'engagementInteractionItem.subject' => [
                'string',
                'required',
            ],
            'engagementInteractionItem.description' => [
                'string',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['direction'] = $this->engagementInteractionItem::DIRECTION_RADIO;
        $this->listsForFields['duration'] = $this->engagementInteractionItem::DURATION_RADIO;
    }
}
