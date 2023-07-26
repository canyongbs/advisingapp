<?php

namespace App\Livewire\ProspectStatus;

use Livewire\Component;
use App\Models\ProspectStatus;

class Create extends Component
{
    public ProspectStatus $prospectStatus;

    public function mount(ProspectStatus $prospectStatus)
    {
        $this->prospectStatus = $prospectStatus;
    }

    public function render()
    {
        return view('livewire.prospect-status.create');
    }

    public function submit()
    {
        $this->validate();

        $this->prospectStatus->save();

        return redirect()->route('admin.prospect-statuses.index');
    }

    protected function rules(): array
    {
        return [
            'prospectStatus.status' => [
                'string',
                'required',
            ],
        ];
    }
}
