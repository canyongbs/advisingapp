<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use App\Settings\CollegeBrandingSettings;

class BrandingBar extends Component
{
    public bool $isVisible = true;

    public ?string $brandingBarText;

    public bool $dismissible = false;

    public ?string $color;

    public function dismiss(): void
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

    #[On('refresh-branding-bar')]
    public function refreshBrandingBar()
    {
        $this->updateVisibility();
    }

    public function render(): View
    {
        return view('vendor.filament-panels.components.branding-bar');
    }

    public function mount(): void
    {
        $brandingSettings = app(CollegeBrandingSettings::class);
        $this->color = $brandingSettings->color;
        $this->brandingBarText = $brandingSettings->college_text;
        $this->dismissible = $brandingSettings->dismissible;
        $this->updateVisibility();
    }

    private function updateVisibility(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->isVisible = ! $user->is_branding_bar_dismissed;
    }
}
