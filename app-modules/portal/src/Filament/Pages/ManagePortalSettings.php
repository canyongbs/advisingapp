<?php

namespace AdvisingApp\Portal\Filament\Pages;

use App\Models\User;
use App\Enums\Feature;
use Filament\Forms\Form;
use App\Models\SettingsProperty;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Gate;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ColorPicker;
use FilamentTiptapEditor\Enums\TiptapOutput;
use AdvisingApp\Portal\Settings\PortalSettings;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ManagePortalSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Portal Settings';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 120;

    protected static string $settings = PortalSettings::class;

    protected static ?string $title = 'Portal Settings';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('audit.view_portal_settings');
    }

    public function mount(): void
    {
        $this->authorize('audit.view_portal_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Branding')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->disk('s3')
                            ->collection('logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                SettingsProperty::getInstance('portal.logo'),
                            )
                            ->columnSpanFull(),
                        ColorPicker::make('primary_color')
                            ->hexColor(),
                        ColorPicker::make('secondary_color')
                            ->hexColor(),
                    ])
                    ->columns(2),
                Section::make('Features')
                    ->schema([
                        Toggle::make('has_applications')
                            ->label('Applications'),
                        Toggle::make('has_message_center')
                            ->label('Message Center'),
                        Toggle::make('has_user_chat')
                            ->label('Realtime Chat')
                            ->disabled(! Gate::check(Feature::RealtimeChat->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Realtime Chat is not a part of your current subscription.'),
                        Toggle::make('has_care_team')
                            ->label('Care Team'),
                        Toggle::make('has_performance_alerts')
                            ->label('Performance Alerts'),
                        Toggle::make('has_emergency_alerts')
                            ->label('Emergency Alerts'),
                        Toggle::make('has_service_management')
                            ->label('Service Management')
                            ->disabled(! Gate::check(Feature::ServiceManagement->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Service Management is not a part of your current subscription.'),
                        Toggle::make('has_notifications')
                            ->label('Portal Notifications'),
                        Toggle::make('has_knowledge_base')
                            ->label('Knowledge Management')
                            ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Knowledge Management is not a part of your current subscription.'),
                        Toggle::make('has_tasks')
                            ->label('Tasks'),
                        Toggle::make('has_files_and_documents')
                            ->label('Files and Documents'),
                        Toggle::make('has_forms')
                            ->label('Forms')
                            ->disabled(! Gate::check(Feature::DynamicForms->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Forms are not a part of your current subscription.'),
                        Toggle::make('has_surveys')
                            ->label('Surveys')
                            ->disabled(! Gate::check(Feature::ConductSurveys->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Surveys are not a part of your current subscription.'),
                    ])
                    ->columns(3),
                Section::make('Footer')
                    ->schema([
                        ColorPicker::make('footer_color')
                            ->label('Color')
                            ->hexColor(),
                        TiptapEditor::make('footer_copyright_statement')
                            ->label('Copyright statement')
                            ->tools(['bold', 'underline', 'italic', 'link'])
                            ->columnSpanFull()
                            ->output(TiptapOutput::Json),
                    ])
                    ->columns(2),
            ]);
    }
}
