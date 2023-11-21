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

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Assist\IntegrationAI\Settings\AISettings;

class ManageAiSettings extends SettingsPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $settings = AISettings::class;

    protected static ?string $title = 'Manage AI Settings';

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->can(['assistant.access_ai_settings']), 403);

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        return [
            AssistantConfiguration::getUrl() => 'Artificial Intelligence',
            $this::getUrl() => 'Manage AI Settings',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('prompt_system_context')
                    ->label('Base Prompt')
                    ->required()
                    ->string()
                    ->rows(12)
                    ->columnSpan('full'),
                TextInput::make('max_tokens')
                    ->label('Max Tokens')
                    ->required()
                    ->numeric()
                    ->columnSpan('1/2'),
                TextInput::make('temperature')
                    ->label('Temperature')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.1)
                    ->minValue(0.0)
                    ->maxValue(2.0)
                    ->columnSpan('1/2'),
            ]);
    }

    public function getSubNavigation(): array
    {
        return (new AssistantConfiguration())->getSubNavigation();
    }
}
