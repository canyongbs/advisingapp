<?php

namespace App\Http\Livewire\CaseItemPriority;

use App\Models\CaseItemPriority;
use Livewire\Component;

class Edit extends Component
{
    public CaseItemPriority $caseItemPriority;

    public function mount(CaseItemPriority $caseItemPriority)
    {
        $this->caseItemPriority = $caseItemPriority;
    }

    public function render()
    {
        return view('livewire.case-item-priority.edit');
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
