<?php

namespace App\Livewire\EngagementInteractionDriver;

use Livewire\Component;
use App\Models\EngagementInteractionDriver;

class Create extends Component
{
    public EngagementInteractionDriver $engagementInteractionDriver;

    public function mount(EngagementInteractionDriver $engagementInteractionDriver)
    {
        $this->engagementInteractionDriver = $engagementInteractionDriver;
    }

    public function render()
    {
        return view('livewire.engagement-interaction-driver.create');
    }

    public function submit()
    {
        $this->validate();

        $this->engagementInteractionDriver->save();

        return redirect()->route('admin.engagement-interaction-drivers.index');
    }

    protected function rules(): array
    {
        return [
            'engagementInteractionDriver.driver' => [
                'string',
                'required',
            ],
        ];
    }
}
