<?php

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages;

use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Filament\Blocks\CaseTypeEmailTemplateButtonBlock;
use AdvisingApp\CaseManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Models\CaseTypeEmailTemplate;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

/** @property-read ?CaseTypeEmailTemplate $template */
class ManageCaseTypeEmailTemplate extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = CaseTypeResource::class;

    #[Locked]
    public CaseEmailTemplateType $type;

    public static ?string $navigationGroup = 'Email Templates';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Form $form): Form
    {
        $roles = CaseTypeEmailTemplateRole::cases();

        if ($this->type === CaseEmailTemplateType::SurveyResponse) {
            $roles = [CaseTypeEmailTemplateRole::Customer];
        }

        return $form
            ->schema([
                Tabs::make('Email template roles')
                    ->persistTab()
                    ->id('email-template-role-tabs')
                    ->tabs(array_map(
                        fn (CaseTypeEmailTemplateRole $role) => Tab::make($role->getLabel())
                            ->schema($this->getEmailTemplateFormSchema())
                            ->statePath($role->value),
                        $roles
                    ))
                    ->columnSpanFull(),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        /** @var CaseType $record */
        $record = $this->getRecord();

        foreach (CaseTypeEmailTemplateRole::cases() as $role) {
            $templateData = $data[$role->value] ?? null;

            if (
                ! $templateData ||
                (blank($templateData['subject']) && blank($templateData['body']))
            ) {
                continue;
            }

            $template = CaseTypeEmailTemplate::firstOrNew([
                'case_type_id' => $record->getKey(),
                'type' => $this->type,
                'role' => $role,
            ]);

            if (! $template->exists && (blank($templateData['subject']) || blank($templateData['body']))) {
                continue;
            }

            $template->subject = $templateData['subject'] ?? $template->subject;
            $template->body = $templateData['body'] ?? $template->body;

            $template->save();
        }

        $this->getSavedNotification()->send();
    }

    /** @return array<int, TiptapEditor> */
    protected function getEmailTemplateFormSchema(): array
    {
        return [
            TiptapEditor::make('subject')
                ->label('Subject')
                ->placeholder('Enter the email subject here...')
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->disableToolbarMenus()
                ->mergeTags(['case number', 'created date', 'updated date', 'status', 'assigned to', 'title', 'type'])
                ->showMergeTagsInBlocksPanel(false)
                ->helperText('You may use “merge tags” to substitute information about a case into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags'),

            TiptapEditor::make('body')
                ->label('Body')
                ->profile('email_template')
                ->placeholder('Enter the email body here...')
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->mergeTags(['case number', 'created date', 'updated date', 'status', 'assigned to', 'title', 'description', 'type'])
                ->blocks([
                    CaseTypeEmailTemplateButtonBlock::class,
                    SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
                ])
                ->columnSpanFull(),
        ];
    }

    protected function fillForm(): void
    {
        /** @var CaseType $record */
        $record = $this->getRecord();

        /** @var Collection<int, CaseTypeEmailTemplate> $templates */
        $templates = $record
            ->templates()
            ->where('type', $this->type)
            ->get();

        /** @var Collection<string, CaseTypeEmailTemplate> $templates */
        $templates = $templates->keyBy(fn (CaseTypeEmailTemplate $template) => $template->role->value);

        $state = [];

        foreach (CaseTypeEmailTemplateRole::cases() as $role) {
            if ($template = $templates[$role->value] ?? null) {
                $state[$role->value] = $template->only(['subject', 'body']);
            }
        }

        $this->form->fill($state);
    }

    #[Computed]
    protected function template(): ?CaseTypeEmailTemplate
    {
        /** @var CaseType $record */
        $record = $this->getRecord();

        return $record->templates()->where('type', $this->type)->first();
    }
}
