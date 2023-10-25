<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SettingsProperty;

class SetupDefaultAssets extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:setup-default-assets';

    /**
     * @var string
     */
    protected $description = 'Setup the default assets.';

    public function handle(): int
    {
        $favicon = SettingsProperty::getInstance('theme.is_favicon_active');
        $favicon->clearMediaCollection('favicon')
            ->addMedia(resource_path('images/default-favicon.png'))
            ->preservingOriginal()
            ->toMediaCollection('favicon');
        $favicon->update(['payload' => true]);

        $logo = SettingsProperty::getInstance('theme.is_logo_active');
        $logo->clearMediaCollection('logo')
            ->addMedia(resource_path('images/default-logo-light.png'))
            ->preservingOriginal()
            ->toMediaCollection('logo');

        $logo->clearMediaCollection('dark_logo')
            ->addMedia(resource_path('images/default-logo-dark.png'))
            ->preservingOriginal()
            ->toMediaCollection('dark_logo');
        $logo->update(['payload' => true]);

        return self::SUCCESS;
    }
}
