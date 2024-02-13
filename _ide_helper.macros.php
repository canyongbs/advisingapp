<?php

namespace Filament\Forms\Components {
    use App\Models\User;

    class Checkbox
    {
        /**
         * @source app/Providers/FilamentServiceProvider.php
         */
        public function lockedWithoutAnyLicenses(User $user, array $licenses): self
        {
            return $this;
        }
    }

    class Toggle
    {
        /**
         * @source app/Providers/FilamentServiceProvider.php
         */
        public function lockedWithoutAnyLicenses(User $user, array $licenses): self
        {
            return $this;
        }
    }
}
