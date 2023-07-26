<?php

namespace App\Livewire\KbItemQuality;

use Livewire\Component;
use App\Models\KbItemQuality;

class Edit extends Component
{
    public KbItemQuality $kbItemQuality;

    public function mount(KbItemQuality $kbItemQuality)
    {
        $this->kbItemQuality = $kbItemQuality;
    }

    public function render()
    {
        return view('livewire.kb-item-quality.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->kbItemQuality->save();

        return redirect()->route('admin.kb-item-qualities.index');
    }

    protected function rules(): array
    {
        return [
            'kbItemQuality.rating' => [
                'string',
                'required',
            ],
        ];
    }
}
