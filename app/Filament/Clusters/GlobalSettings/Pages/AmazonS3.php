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

namespace App\Filament\Clusters\GlobalSettings\Pages;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Clusters\GlobalSettings;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;

/**
 * @property ComponentContainer $form
 */
class AmazonS3 extends Page implements HasForms
{
    use InteractsWithForms;
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Amazon S3 Settings';

    protected static ?string $navigationLabel = 'Amazon S3';

    protected static string $view = 'filament.clusters.global-settings.pages.amazon-s3';

    protected static ?string $cluster = GlobalSettings::class;

    protected static ?string $navigationGroup = 'Product Integrations';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('manage_s3_settings');
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public function fillForm(): void
    {
        $this->callHook('beforeFill');

        /** @var TenantConfig $config */
        $config = Tenant::current()->config;

        $data = $this->mutateFormDataBeforeFill($config->mail->toArray());

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('fromAddress')
                    ->label('From Address')
                    ->email()
                    ->required(),
                TextInput::make('fromName')
                    ->label('From Name')
                    ->string()
                    ->maxLength(150)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        DB::beginTransaction();

        try {
            $this->callHook('beforeValidate');

            $data = [
                ...$this->data,
                ...$this->form->getState(),
            ];

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            /** @var Tenant $tenant */
            $tenant = Tenant::current();

            /** @var TenantConfig $config */
            $config = $tenant->config;

            $config->mail = TenantMailConfig::from($data);

            $tenant->config = $config;

            $tenant->save();

            $this->callHook('afterSave');

            Notification::make()
                ->title('S3 Settings Updated!')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            Notification::make()
                ->title('Something went wrong, if this continues please contact support.')
                ->danger()
                ->send();
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
