<?php

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages;

use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use App\Features\CaseTypeNotificationFeature;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCaseTypeNotifications extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = CaseTypeResource::class;

    protected static ?string $title = 'Notifications';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return CaseTypeNotificationFeature::active() && parent::canAccess($parameters);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Notifications and Alerts')
                    ->description('This page is used to configure notifications and alerts for this case type.')
                    ->schema([
                        ViewField::make('settings')
                            ->rules(['array'])
                            ->view('case-management::filament.resources.case-type-resource.pages.edit-case-type-notifications.matrix'),
                    ])
                    ->extraAttributes(['class' => 'fi-section-no-content-padding']),
            ]);
    }

    /**
     * @return array<string>
     */
    protected function generateSettingsAttributeList(): array
    {
        $attributes = [];

        foreach (['managers', 'auditors', 'customers'] as $role) {
            foreach (
                [
                    'case_created',
                    'case_assigned',
                    'case_update',
                    'case_status_change',
                    'case_closed',
                    'survey_response',
                ] as $event
            ) {
                $attributes[] = "is_{$role}_{$event}_email_enabled";
                $attributes[] = "is_{$role}_{$event}_notification_enabled";
            }
        }

        return $attributes;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['settings'] = $record->only($this->generateSettingsAttributeList());

        return $data;
    }

    /**
     * @param  array{settings: array<string, mixed>, ...} $data
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = [
            ...$data,
            ...collect($data['settings'])
                ->only($this->generateSettingsAttributeList())
                ->filter(fn (mixed $value): bool => is_bool($value))
                ->all(),
        ];

        unset($data['settings']);

        return $data;
    }
}
