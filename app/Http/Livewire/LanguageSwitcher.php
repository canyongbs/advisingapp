<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public array $languages = [];

    public string $currentLanguage;

    public function mount()
    {
        $this->languages       = config('project.supported_languages');
        $this->currentLanguage = app()->getLocale();
    }

    public function changeLocale(string $localeCode)
    {
        auth()->user()->update(['locale' => $localeCode]);

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
