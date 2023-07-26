<?php

namespace App\Livewire\KbItemStatus;

use Livewire\Component;
use App\Models\KbItemStatus;

class Create extends Component
{
    public KbItemStatus $kbItemStatus;

    public function mount(KbItemStatus $kbItemStatus)
    {
        $this->kbItemStatus = $kbItemStatus;
    }

    public function render()
    {
        return view('livewire.kb-item-status.create');
    }

    public function submit()
    {
        $this->validate();

        $this->kbItemStatus->save();

        return redirect()->route('admin.kb-item-statuses.index');
    }

    protected function rules(): array
    {
        return [
            'kbItemStatus.status' => [
                'string',
                'required',
            ],
        ];
    }
}
