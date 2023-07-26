<?php

namespace App\Livewire\JourneyEmailItem;

use Livewire\Component;
use App\Models\JourneyEmailItem;

class Edit extends Component
{
    public array $listsForFields = [];

    public JourneyEmailItem $journeyEmailItem;

    public function mount(JourneyEmailItem $journeyEmailItem)
    {
        $this->journeyEmailItem = $journeyEmailItem;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.journey-email-item.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->journeyEmailItem->save();

        return redirect()->route('admin.journey-email-items.index');
    }

    protected function rules(): array
    {
        return [
            'journeyEmailItem.name' => [
                'string',
                'required',
            ],
            'journeyEmailItem.body' => [
                'string',
                'required',
            ],
            'journeyEmailItem.start' => [
                'required',
                'date_format:' . config('project.datetime_format'),
            ],
            'journeyEmailItem.end' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'journeyEmailItem.active' => [
                'nullable',
                'in:' . implode(',', array_keys($this->listsForFields['active'])),
            ],
            'journeyEmailItem.frequency' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['frequency'])),
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['active'] = $this->journeyEmailItem::ACTIVE_RADIO;
        $this->listsForFields['frequency'] = $this->journeyEmailItem::FREQUENCY_RADIO;
    }
}
