<?php

namespace App\Filament\Clusters\GlobalSettings\Pages;

use App\Filament\Clusters\GlobalSettings;
use App\Models\Tenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Throwable;

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

    public static function canAccess(): bool
    {
        // TODO: Add a custom permissions that is checked here
        return true;
    }

    public ?array $data = [];

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
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
                    ->required()
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
