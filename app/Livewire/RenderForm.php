<?php

namespace App\Livewire;

use Livewire\Component;
use Assist\Form\Models\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class RenderForm extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $show = true;

    public Form $form;

    public ?array $data = [];

    public function render(): View
    {
        return view('livewire.render-form')
            ->title($this->form->name);
    }
}
