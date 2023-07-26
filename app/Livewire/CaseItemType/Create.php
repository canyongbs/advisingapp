<?php

namespace App\Livewire\CaseItemType;

use Livewire\Component;
use Assist\CaseModule\Models\CaseItemType;

class Create extends Component
{
    public CaseItemType $caseItemType;

    public function mount(CaseItemType $caseItemType)
    {
        $this->caseItemType = $caseItemType;
    }

    public function render()
    {
        return view('livewire.case-item-type.create');
    }

    public function submit()
    {
        $this->validate();

        $this->caseItemType->save();

        return redirect()->route('admin.case-item-types.index');
    }

    protected function rules(): array
    {
        return [
            'caseItemType.type' => [
                'string',
                'required',
            ],
        ];
    }
}
