<?php

namespace AdvisingApp\MultifactorAuthentication\Livewire;

use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class MultifactorAuthentication extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function render()
    {
        return view('multifactor-authentication::livewire.multifactor-authentication');
    }
}
