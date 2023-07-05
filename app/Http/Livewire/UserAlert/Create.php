<?php

namespace App\Http\Livewire\UserAlert;

use App\Models\User;
use App\Models\UserAlert;
use Livewire\Component;

class Create extends Component
{
    public array $users = [];

    public UserAlert $userAlert;

    public array $listsForFields = [];

    public function mount(UserAlert $userAlert)
    {
        $this->userAlert = $userAlert;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.user-alert.create');
    }

    public function submit()
    {
        $this->validate();

        $this->userAlert->save();
        $this->userAlert->users()->sync($this->users);

        return redirect()->route('admin.user-alerts.index');
    }

    protected function rules(): array
    {
        return [
            'userAlert.message' => [
                'string',
                'required',
            ],
            'userAlert.link' => [
                'string',
                'nullable',
            ],
            'users' => [
                'required',
                'array',
            ],
            'users.*.id' => [
                'integer',
                'exists:users,id',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['users'] = User::pluck('name', 'id')->toArray();
    }
}
