<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\IntegrationGoogleAnalytics\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Assist\IntegrationGoogleAnalytics\Settings\GoogleAnalyticsSettings;

class ManageGoogleAnalyticsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GoogleAnalyticsSettings::class;

    protected static ?string $title = 'Google Analytics Settings';

    protected static ?string $navigationLabel = 'Google Analytics';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('integration-google-analytics.view_google_analytics_settings');
    }

    public function mount(): void
    {
        $this->authorize('integration-google-analytics.view_google_analytics_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Toggle::make('is_enabled')
                    ->label('Enabled')
                    ->live(),
                TextInput::make('id')
                    ->visible(fn (Get $get) => $get('is_enabled')),
                // TODO: add key value options?
            ]);
    }
}
