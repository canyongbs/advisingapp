<?php

namespace App\Livewire\EngagementInteractionOutcome;

use Livewire\Component;
use App\Models\EngagementInteractionOutcome;

class Create extends Component
{
    public EngagementInteractionOutcome $engagementInteractionOutcome;

    public function mount(EngagementInteractionOutcome $engagementInteractionOutcome)
    {
        $this->engagementInteractionOutcome = $engagementInteractionOutcome;
    }

    public function render()
    {
        return view('livewire.engagement-interaction-outcome.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementInteractionOutcome->save();

        return redirect()->route('admin.engagement-interaction-outcomes.index');
    }

    protected function rules(): array
    {
        return [
            'engagementInteractionOutcome.outcome' => [
                'string',
                'required',
            ],
        ];
    }
}
