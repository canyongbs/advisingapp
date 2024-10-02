<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class DismissBrandingBar extends Component
{
    public $isVisible = true;

    public function dismiss()
    {
        // Hide the branding bar
        $this->isVisible = false;

        // Update a field for the user in the database
        $currentUser = User::find(auth()->user()->id);

        if (! empty($currentUser)) {
            $currentUser->update([
                'is_branding_bar_dismissed' => true,
            ]);
        }
    }

    public function render(): View
    {
        return view('vendor.filament-panels.components.dismiss-branding-bar');
    }
}
