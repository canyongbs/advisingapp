<?php

namespace App\Livewire\ProspectSource;

use Livewire\Component;
use App\Models\ProspectSource;

class Create extends Component
{
    public ProspectSource $prospectSource;

    public function mount(ProspectSource $prospectSource)
    {
        $this->prospectSource = $prospectSource;
    }

    public function render()
    {
        return view('livewire.prospect-source.create');
    }

    public function submit()
    {
        $this->validate();

        $this->prospectSource->save();

        return redirect()->route('admin.prospect-sources.index');
    }

    protected function rules(): array
    {
        return [
            'prospectSource.source' => [
                'string',
                'required',
            ],
        ];
    }
}
