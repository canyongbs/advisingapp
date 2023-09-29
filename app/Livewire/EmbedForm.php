<?php

namespace App\Livewire;

use Livewire\Component;
use Assist\Form\Models\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Concerns\InteractsWithForms;

class EmbedForm extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $show = true;

    public Form $embed;

    public ?array $data = [];

    public function mount(Form $embed): void
    {
        $embed->loadMissing('items');

        $this->form->fill();
    }

    public function form(FilamentForm $form): FilamentForm
    {
        $fields = $this
            ->embed
            ->items
            ->map(fn ($item) => match ($item->type) {
                'text_input' => TextInput::make($item->key)
                    ->label($item->label),
                'text_area' => Textarea::make($item->key)
                    ->label($item->label),
                'select' => Select::make($item->key)
                    ->label($item->label)
                    ->options($item->content['options'])
            })
            ->toArray();

        return $form
            ->schema($fields)
            ->statePath('data')
            ->model($this->embed);
    }

    public function create(): void
    {
        $this->embed->submissions()->create(['content' => $this->form->getState()]);

        $this->show = false;

        $this->form->fill();
    }

    public function resetForm(): void
    {
        $this->form->fill();

        $this->dispatch('close-modal', id: 'reset');

        $this->show = true;
    }

    public function render(): View
    {
        return view('livewire.embed-form')
            ->title($this->embed->name);
    }
}
