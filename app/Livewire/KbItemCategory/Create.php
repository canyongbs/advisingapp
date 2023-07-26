<?php

namespace App\Livewire\KbItemCategory;

use Livewire\Component;
use App\Models\KbItemCategory;

class Create extends Component
{
    public KbItemCategory $kbItemCategory;

    public function mount(KbItemCategory $kbItemCategory)
    {
        $this->kbItemCategory = $kbItemCategory;
    }

    public function render()
    {
        return view('livewire.kb-item-category.create');
    }

    public function submit()
    {
        $this->validate();

        $this->kbItemCategory->save();

        return redirect()->route('admin.kb-item-categories.index');
    }

    protected function rules(): array
    {
        return [
            'kbItemCategory.category' => [
                'string',
                'required',
            ],
        ];
    }
}
