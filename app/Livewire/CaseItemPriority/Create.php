<?php

namespace App\Livewire\CaseItemPriority;

use Livewire\Component;
use Assist\CaseModule\Models\CaseItemPriority;

class Create extends Component
{
    public CaseItemPriority $caseItemPriority;

    public function mount(CaseItemPriority $caseItemPriority)
    {
        $this->caseItemPriority = $caseItemPriority;
    }

    public function render()
    {
        return view('livewire.case-item-priority.create');
    }

    public function submit()
    {
        $this->validate();

        $this->caseItemPriority->save();

        return redirect()->route('admin.case-item-priorities.index');
    }

    protected function rules(): array
    {
        return [
            'caseItemPriority.priority' => [
                'string',
                'required',
            ],
        ];
    }
}
