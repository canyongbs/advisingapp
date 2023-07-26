<?php

namespace App\Livewire\EngagementEmailItem;

use Livewire\Component;
use App\Models\EngagementEmailItem;

class Create extends Component
{
    public EngagementEmailItem $engagementEmailItem;

    public function mount(EngagementEmailItem $engagementEmailItem)
    {
        $this->engagementEmailItem = $engagementEmailItem;
    }

    public function render()
    {
        return view('livewire.engagement-email-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementEmailItem->save();

        return redirect()->route('admin.engagement-email-items.index');
    }

    protected function rules(): array
    {
        return [
            'engagementEmailItem.email' => [
                'email:rfc',
                'required',
            ],
            'engagementEmailItem.subject' => [
                'string',
                'required',
            ],
            'engagementEmailItem.body' => [
                'string',
                'required',
            ],
        ];
    }
}
