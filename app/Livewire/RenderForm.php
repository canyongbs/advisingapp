<?php

namespace App\Livewire;

use Livewire\Component;
use Assist\Form\Models\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Assist\Form\Filament\Blocks\SelectFormFieldBlock;
use Assist\Form\Filament\Blocks\TextAreaFormFieldBlock;
use Assist\Form\Filament\Blocks\TextInputFormFieldBlock;

class RenderForm extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $show = true;

    public Form $form;

    public ?array $data = [];

    public function mount(Form $form): void
    {
        $form->loadMissing('fields');

        $this->getForm('form')->fill();
    }

    public function form(FilamentForm $form): FilamentForm
    {
        $fields = $this
            ->form
            ->fields
            ->map(fn ($item) => match ($item->type) {
                'text_input' => TextInputFormFieldBlock::display($item),
                'text_area' => TextAreaFormFieldBlock::display($item),
                'select' => SelectFormFieldBlock::display($item),
            })
            ->toArray();

        return $form
            ->schema($fields)
            ->statePath('data')
            ->model($this->form);
    }

    public function submit(): void
    {
        $this->form->submissions()->create(['content' => $this->getForm('form')->getState()]);

        $this->show = false;

        $this->getForm('form')->fill();
    }

    public function resetForm(): void
    {
        $this->getForm('form')->fill();

        $this->dispatch('close-modal', id: 'reset');

        $this->show = true;
    }

    public function render(): View
    {
        return view('livewire.render-form')
            ->title($this->form->name);
    }
}
