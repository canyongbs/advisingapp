<?php

namespace App\Http\Livewire\KbItemQuality;

use App\Models\KbItemQuality;
use Livewire\Component;

class Create extends Component
{
    public KbItemQuality $kbItemQuality;

    public function mount(KbItemQuality $kbItemQuality)
    {
        $this->kbItemQuality = $kbItemQuality;
    }

    public function render()
    {
        return view('livewire.kb-item-quality.create');
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
