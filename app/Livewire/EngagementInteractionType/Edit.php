<?php

namespace App\Livewire\EngagementInteractionType;

use Livewire\Component;
use App\Models\EngagementInteractionType;

class Edit extends Component
{
    public EngagementInteractionType $engagementInteractionType;

    public function mount(EngagementInteractionType $engagementInteractionType)
    {
        $this->engagementInteractionType = $engagementInteractionType;
    }

    public function render()
    {
        return view('livewire.engagement-interaction-type.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementInteractionType->save();

        return redirect()->route('admin.engagement-interaction-types.index');
    }

    protected function rules(): array
    {
        return [
            'engagementInteractionType.type' => [
                'string',
                'nullable',
            ],
        ];
    }
}
