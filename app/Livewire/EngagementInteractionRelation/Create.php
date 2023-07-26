<?php

namespace App\Livewire\EngagementInteractionRelation;

use Livewire\Component;
use App\Models\EngagementInteractionRelation;

class Create extends Component
{
    public EngagementInteractionRelation $engagementInteractionRelation;

    public function mount(EngagementInteractionRelation $engagementInteractionRelation)
    {
        $this->engagementInteractionRelation = $engagementInteractionRelation;
    }

    public function render()
    {
        return view('livewire.engagement-interaction-relation.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementInteractionRelation->save();

        return redirect()->route('admin.engagement-interaction-relations.index');
    }

    protected function rules(): array
    {
        return [
            'engagementInteractionRelation.relation' => [
                'string',
                'required',
            ],
        ];
    }
}
