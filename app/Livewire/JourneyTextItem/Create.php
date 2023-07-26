<?php

namespace App\Livewire\JourneyTextItem;

use Livewire\Component;
use App\Models\JourneyTextItem;

class Create extends Component
{
    public array $listsForFields = [];

    public JourneyTextItem $journeyTextItem;

    public function mount(JourneyTextItem $journeyTextItem)
    {
        $this->journeyTextItem = $journeyTextItem;
        $this->journeyTextItem->active = 'N';
        $this->journeyTextItem->frequency = '1';
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.journey-text-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->journeyTextItem->save();

        return redirect()->route('admin.journey-text-items.index');
    }

    protected function rules(): array
    {
        return [
            'journeyTextItem.name' => [
                'string',
                'nullable',
            ],
            'journeyTextItem.text' => [
                'string',
                'max:159',
                'nullable',
            ],
            'journeyTextItem.start' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'journeyTextItem.end' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'journeyTextItem.active' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['active'])),
            ],
            'journeyTextItem.frequency' => [
                'nullable',
                'in:' . implode(',', array_keys($this->listsForFields['frequency'])),
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['active'] = $this->journeyTextItem::ACTIVE_RADIO;
        $this->listsForFields['frequency'] = $this->journeyTextItem::FREQUENCY_RADIO;
    }
}
