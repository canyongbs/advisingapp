<?php

namespace App\Livewire\SupportPage;

use Livewire\Component;
use App\Models\SupportPage;

class Edit extends Component
{
    public SupportPage $supportPage;

    public function mount(SupportPage $supportPage)
    {
        $this->supportPage = $supportPage;
    }

    public function render()
    {
        return view('livewire.support-page.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->supportPage->save();

        return redirect()->route('admin.support-pages.index');
    }

    protected function rules(): array
    {
        return [
            'supportPage.title' => [
                'string',
                'required',
            ],
            'supportPage.body' => [
                'string',
                'required',
            ],
        ];
    }
}
