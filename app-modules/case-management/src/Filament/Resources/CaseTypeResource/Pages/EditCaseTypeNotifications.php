<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages;

use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
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
        return parent::canAccess($parameters);
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
