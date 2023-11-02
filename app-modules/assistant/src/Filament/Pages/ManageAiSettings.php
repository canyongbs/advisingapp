<?php

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
