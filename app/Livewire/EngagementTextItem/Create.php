<?php

namespace App\Livewire\EngagementTextItem;

use Livewire\Component;
use App\Models\EngagementTextItem;

class Create extends Component
{
    public EngagementTextItem $engagementTextItem;

    public function mount(EngagementTextItem $engagementTextItem)
    {
        $this->engagementTextItem = $engagementTextItem;
    }

    public function render()
    {
        return view('livewire.engagement-text-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementTextItem->save();

        return redirect()->route('admin.engagement-text-items.index');
    }

    protected function rules(): array
    {
        return [
            'engagementTextItem.mobile' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'required',
            ],
            'engagementTextItem.message' => [
                'string',
                'max:159',
                'nullable',
            ],
        ];
    }
}
