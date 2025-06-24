<?php

namespace AdvisingApp\Ai\Filament\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;
use App\Features\QnaAdvisorFeature;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Validation\Rule;

class ManageAiQnaAdvisorSettings extends ManageAiICustomAdvisorSettings
{
    protected static string $settings = AiQnaAdvisorSettings::class;

    protected static ?string $title = 'QnA Advisor';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return QnaAdvisorFeature::active() && $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('allow_selection_of_model')
                    ->label('Allow selection of model?')
                    ->helperText('If enabled, users can select a model when creating or editing QnA advisors.')
                    ->columnSpanFull()
                    ->live(),
                Select::make('preselected_model')
                    ->label('Select Model')
                    ->options(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModelsAsSelectOptions())
                    ->searchable()
                    ->helperText('This model will be the model used for QnA advisors.')
                    ->columnSpanFull()
                    ->required()
                    ->visible(fn (Get $get): bool => ! $get('allow_selection_of_model'))
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModels())),
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
