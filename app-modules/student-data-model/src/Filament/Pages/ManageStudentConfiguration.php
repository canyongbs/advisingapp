<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Filament\Pages;

use Throwable;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

use function Filament\Support\is_app_url;

use Filament\Support\Facades\FilamentView;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;

class ManageStudentConfiguration extends SettingsPage
{
    protected static string $settings = ManageStudentConfigurationSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $navigationLabel = 'Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_enabled')
                    ->label('Enable')
                    ->default(false),
            ]);
    }

    public function save(): void
    {
        try {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();

            $this->callHook('afterValidate');
            $settings = app(static::getSettings());
            $this->callHook('beforeSave');
            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            Notification::make()
                ->title('Students Configured!')
                ->success()
                ->send();

            if ($redirectUrl = $this->getRedirectUrl()) {
                $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
            }
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Something went wrong, if this continues please contact support.')
                ->danger()
                ->send();
        }
    }

    public function getRedirectUrl(): ?string
    {
        return ManageStudentConfiguration::getUrl();
    }
}
