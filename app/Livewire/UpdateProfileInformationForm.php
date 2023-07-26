<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UpdateProfileInformationForm extends Component
{
    use AuthorizesRequests;

    public $state = [];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.email' => 'email',
    ];

    public function mount()
    {
        $this->state = auth()->user()->withoutRelations()->toArray();
    }

    public function updateProfileInformation()
    {
        $this->authorize('auth_profile_edit');

        $this->resetErrorBag();

        $validatedData = $this->validate();

        auth()->user()->update($validatedData['state']);

        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.update-profile-information-form');
    }

    protected function rules()
    {
        return [
            'state.name' => ['required', 'string', 'max:255'],
            'state.email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . auth()->id(),
            ],
        ];
    }
}
