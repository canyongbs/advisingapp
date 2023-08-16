<?php

namespace Assist\Audit\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Assist\Audit\Settings\AuditSettings;

class ManageAuditSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AuditSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ...
            ]);
    }
}
