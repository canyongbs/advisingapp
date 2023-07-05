<?php

namespace App\Http\Livewire\CaseItemType;

use App\Models\CaseItemType;
use Livewire\Component;

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
