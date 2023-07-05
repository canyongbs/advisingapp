<?php

namespace App\Http\Livewire\ProspectStatus;

use App\Models\ProspectStatus;
use Livewire\Component;

class Edit extends Component
{
    public ProspectStatus $prospectStatus;

    public function mount(ProspectStatus $prospectStatus)
    {
        $this->prospectStatus = $prospectStatus;
    }

    public function render()
    {
        return view('livewire.prospect-status.edit');
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
