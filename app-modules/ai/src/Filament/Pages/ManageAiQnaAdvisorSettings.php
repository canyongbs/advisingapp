<?php

namespace AdvisingApp\Ai\Filament\Pages;

use AdvisingApp\Ai\Settings\AiQnAAdvisorSettings;
use App\Features\QnAAdvisorFeature;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class ManageAiQnaAdvisorSettings extends ManageAiICustomAdvisorSettings
{
    protected static string $settings = AiQnAAdvisorSettings::class;

    protected static ?string $title = 'QnA Advisor';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return QnAAdvisorFeature::active() && $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('instructions')
                    ->label('Instructions')
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('background_information')
                    ->label('Background Information')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
