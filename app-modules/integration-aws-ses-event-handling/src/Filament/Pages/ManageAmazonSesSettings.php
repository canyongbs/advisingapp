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

namespace AdvisingApp\IntegrationAwsSesEventHandling\Filament\Pages;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Toggle;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\GlobalSettings;

use function Filament\Support\is_app_url;

use Filament\Support\Facades\FilamentView;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;

class ManageAmazonSesSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SesSettings::class;

    protected static ?string $title = 'Amazon SES Settings';

    protected static ?string $navigationLabel = 'Amazon SES';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationGroup = 'Product Integrations';

    protected static ?string $cluster = GlobalSettings::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('integration-aws-ses-event-handling.view_ses_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('isDemoModeEnabled')
                    ->label('Demo Mode')
                    ->live(),
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('configuration_set')
                            ->label('Configuration Set'),
                        TextInput::make('fromAddress')
                            ->label('From Address')
                            ->email()
                            ->required(),
                        TextInput::make('fromName')
                            ->label('From Name')
                            ->string()
                            ->maxLength(150)
                            ->required(),
                        Section::make()
                            ->heading('SMTP Settings')
                            ->schema([
                                TextInput::make('smtp_host')
                                    ->label('Host')
                                    ->nullable(),
                                TextInput::make('smtp_port')
                                    ->label('Port')
                                    ->integer()
                                    ->required(),
                                TextInput::make('smtp_encryption')
                                    ->label('Encryption')
                                    ->nullable(),
                                TextInput::make('smtp_username')
                                    ->label('Username')
                                    ->nullable(),
                                TextInput::make('smtp_password')
                                    ->label('Password')
                                    ->nullable(),
                                TextInput::make('smtp_timeout')
                                    ->label('Timeout')
                                    ->integer()
                                    ->nullable(),
                                TextInput::make('smtp_local_domain')
                                    ->label('Local Domain')
                                    ->nullable(),
                            ]),
                    ])
                    ->visible(fn (Get $get): bool => ! $get('isDemoModeEnabled')),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();
            DB::connection('landlord')->beginTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            /** @var Tenant $tenant */
            $tenant = Tenant::current();

            /** @var TenantConfig $config */
            $config = $tenant->config;

            if ($data['isDemoModeEnabled']) {
                $config->mail->isDemoModeEnabled = $data['isDemoModeEnabled'];
            } else {
                $config->mail->isDemoModeEnabled = $data['isDemoModeEnabled'];
                $config->mail->fromAddress = $data['fromAddress'];
                $config->mail->fromName = $data['fromName'];
                $config->mail->mailers->smtp->host = $data['smtp_host'];
                $config->mail->mailers->smtp->port = $data['smtp_port'];
                $config->mail->mailers->smtp->encryption = $data['smtp_encryption'];
                $config->mail->mailers->smtp->username = $data['smtp_username'];
                $config->mail->mailers->smtp->password = $data['smtp_password'];
                $config->mail->mailers->smtp->timeout = $data['smtp_timeout'];
                $config->mail->mailers->smtp->localDomain = $data['smtp_local_domain'];
            }

            $tenant->config = $config;

            $tenant->save();

            unset(
                $data['isDemoModeEnabled'],
                $data['fromAddress'],
                $data['fromName'],
                $data['smtp_host'],
                $data['smtp_port'],
                $data['smtp_encryption'],
                $data['smtp_username'],
                $data['smtp_password'],
                $data['smtp_timeout'],
                $data['smtp_local_domain'],
            );

            $settings = app(static::getSettings());

            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
            DB::connection('landlord')->commit();
        } catch (Halt $exception) {
            if ($exception->shouldRollbackDatabaseTransaction()) {
                $this->rollBackDatabaseTransaction();
                DB::connection('landlord')->rollBack();
            } else {
                $this->commitDatabaseTransaction();
                DB::connection('landlord')->commit();
            }

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();
            DB::connection('landlord')->rollBack();

            throw $exception;
        }

        $this->rememberData();

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $settings = app(static::getSettings());

        /** @var Tenant $tenant */
        $tenant = Tenant::current();

        /** @var TenantConfig $config */
        $config = $tenant->config;

        $data = $this->mutateFormDataBeforeFill(
            [
                ...$settings->toArray(),
                'isDemoModeEnabled' => $config->mail->isDemoModeEnabled ?? false,
                'fromAddress' => $config->mail->fromAddress,
                'fromName' => $config->mail->fromName,
                'smtp_host' => $config->mail->mailers->smtp->host,
                'smtp_port' => $config->mail->mailers->smtp->port,
                'smtp_encryption' => $config->mail->mailers->smtp->encryption,
                'smtp_username' => $config->mail->mailers->smtp->username,
                'smtp_password' => $config->mail->mailers->smtp->password,
                'smtp_timeout' => $config->mail->mailers->smtp->timeout,
                'smtp_local_domain' => $config->mail->mailers->smtp->localDomain,
            ]
        );

        $this->form->fill($data);

        $this->callHook('afterFill');
    }
}
