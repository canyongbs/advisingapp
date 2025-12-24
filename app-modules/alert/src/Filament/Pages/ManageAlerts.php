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

namespace AdvisingApp\Alert\Filament\Pages;

use AdvisingApp\Alert\Models\AlertConfiguration;
use App\Features\AlertConfigurationFeature;
use App\Filament\Clusters\ConstituentManagement;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageAlerts extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationLabel = 'Alerts';

    protected string $view = 'alert::filament.pages.manage-alerts';

    protected static ?int $navigationSort = 50;

    protected static ?string $title = 'Student Alerts';

    protected static string | UnitEnum | null $navigationGroup = 'Students';

    /** @var array<string, mixed> $data */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        return AlertConfigurationFeature::active() && $user->can(['settings.view-any']);
    }

    public function mount(): void
    {
        $this->form->fill($this->getFormData());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components($this->getFormSchema())
            ->disabled(! auth()->user()->can('settings.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('settings.*.update')) {
            return;
        }

        DB::beginTransaction();

        try {
            $data = $this->form->getState();

            $alertConfigurations = AlertConfiguration::with('configuration')->orderBy('id')->get();

            foreach ($alertConfigurations as $config) {
                $configId = (string) $config->id;

                if (! isset($data[$configId])) {
                    continue;
                }

                $configData = $data[$configId];

                $config->is_enabled = $configData['is_enabled'] ?? false;
                $config->save();

                if ($config->configuration) {
                    $configurationData = [];

                    foreach ($config->configuration->getFillable() as $field) {
                        if (isset($configData[$field])) {
                            $configurationData[$field] = $configData[$field];
                        }
                    }

                    if (! empty($configurationData)) {
                        $config->configuration->update($configurationData);
                    }
                }
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Alert configurations saved')
                ->body('Your alert settings have been updated successfully.')
                ->send();
        } catch (ValidationException $exception) {
            DB::rollBack();

            throw $exception;
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            Notification::make()
                ->title('Something went wrong, if this continues please contact support.')
                ->danger()
                ->send();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFormData(): array
    {
        if (! AlertConfigurationFeature::active()) {
            return [];
        }

        $data = [];

        $alertConfigurations = AlertConfiguration::with('configuration')->orderBy('id')->get();

        foreach ($alertConfigurations as $config) {
            $configData = [
                'is_enabled' => $config->is_enabled,
            ];

            if ($config->configuration) {
                foreach ($config->configuration->getAttributes() as $key => $value) {
                    if (! in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $configData[$key] = $value;
                    }
                }
            }

            $data[(string) $config->id] = $configData;
        }

        return $data;
    }

    /**
     * @return array<Component>
     */
    protected function getFormSchema(): array
    {
        if (! AlertConfigurationFeature::active()) {
            return [];
        }

        $alertConfigurations = AlertConfiguration::with('configuration')->orderBy('id')->get();
        $innerSections = [];

        foreach ($alertConfigurations as $config) {
            $handler = $config->preset->getHandler();

            $components = [
                Toggle::make('is_enabled')
                    ->label('Enable Alert')
                    ->inline(false),
            ];

            $configurationForm = $handler->configurationForm();

            if (! empty($configurationForm)) {
                $components = array_merge($components, $configurationForm);
            }

            $innerSections[] = Section::make($handler->getName())
                ->description($handler->getDescription())
                ->statePath((string) $config->id)
                ->schema($components)
                ->columns(2)
                ->collapsible(false);
        }

        return [
            Section::make()
                ->columns()
                ->schema([
                    Heading::make()
                        ->content('Student Alerts'),
                    Paragraph::make()
                        ->content('Student Alerts help your institution identify students who may benefit from proactive outreach or additional support. Advising App evaluates SIS data such as grades, withdrawals, course repeats, GPA, demographic indicators, and other student information already provided to your institution. On this page you can enable or disable specific alerts, and configure any thresholds that apply (such as GPA levels or age criteria). Only the alerts that are turned on will be generated for your students.'),
                    ...$innerSections,
                ]),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        if (! auth()->user()->can('settings.*.update')) {
            return [];
        }

        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }
}
