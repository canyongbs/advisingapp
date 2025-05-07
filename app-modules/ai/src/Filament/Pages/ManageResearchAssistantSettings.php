<?php

namespace AdvisingApp\Ai\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Textarea;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Filament\Clusters\GlobalArtificialIntelligence;

class ManageResearchAssistantSettings extends SettingsPage
{
    protected static string $settings = AiIntegrationsSettings::class;

    protected static ?string $title = 'Research Advisor Settings';

    protected static ?string $navigationLabel = 'Research Advisor';

    protected static ?int $navigationSort = 25;

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('Institutional Context')
                    ->rows(10)
                    ->label('Institutional Context')
            ])
            ->columns(1);
    }
}
