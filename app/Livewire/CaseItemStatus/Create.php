<?php

namespace App\Livewire\CaseItemStatus;

use Livewire\Component;
use Assist\CaseModule\Models\CaseItemStatus;

class Create extends Component
{
    public CaseItemStatus $caseItemStatus;

    public function mount(CaseItemStatus $caseItemStatus)
    {
        $this->caseItemStatus = $caseItemStatus;
    }

    public function render()
    {
        return view('livewire.case-item-status.create');
    }

    public function submit()
    {
        $this->validate();

        $this->caseItemStatus->save();

        return redirect()->route('admin.case-item-statuses.index');
    }

    protected function rules(): array
    {
        return [
            'caseItemStatus.status' => [
                'string',
                'required',
            ],
        ];
    }
}
