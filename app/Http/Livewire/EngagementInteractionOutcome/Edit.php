<?php

namespace App\Http\Livewire\EngagementInteractionOutcome;

use App\Models\EngagementInteractionOutcome;
use Livewire\Component;

class Edit extends Component
{
    public EngagementInteractionOutcome $engagementInteractionOutcome;

    public function mount(EngagementInteractionOutcome $engagementInteractionOutcome)
    {
        $this->engagementInteractionOutcome = $engagementInteractionOutcome;
    }

    public function render()
    {
        return view('livewire.engagement-interaction-outcome.edit');
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
