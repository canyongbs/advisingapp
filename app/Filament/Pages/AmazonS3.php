<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Pages;

use App\Filament\Clusters\ProductIntegrations;
use App\Models\Tenant;
use App\Models\User;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property Schema $form
 */
class AmazonS3 extends Page implements HasForms
{
    use InteractsWithForms;
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;

    protected static ?string $title = 'Amazon S3 Settings';

    protected static ?string $navigationLabel = 'Amazon S3';

    protected string $view = 'filament.pages.amazon-s3';

    protected static ?string $cluster = ProductIntegrations::class;

    protected static ?int $navigationSort = 100;

    /** @var array<mixed> $data */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
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

        $data = $this->mutateFormDataBeforeFill(
            [
                's3' => $config->s3Filesystem->toArray(),
                's3-public' => $config->s3PublicFilesystem->toArray(),
            ]
        );

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('S3 Filesystem Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('s3.key')
                            ->label('Key')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3.secret')
                            ->label('Secret')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3.region')
                            ->label('Region')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('s3.bucket')
                            ->label('Bucket')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3.url')
                            ->label('URL')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('s3.endpoint')
                            ->label('Endpoint')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        Toggle::make('s3.usePathStyleEndpoint')
                            ->label('Use Path Style Endpoint')
                            ->default(false),
                        Toggle::make('s3.throw')
                            ->label('Throw')
                            ->default(false),
                        TextInput::make('s3.root')
                            ->label('Root')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                    ])->visible(app()->environment('local')),
                Section::make('S3 Public Filesystem Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('s3-public.key')
                            ->label('Key')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3-public.secret')
                            ->label('Secret')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3-public.region')
                            ->label('Region')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('s3-public.bucket')
                            ->label('Bucket')
                            ->string()
                            ->nullable()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        TextInput::make('s3-public.url')
                            ->label('URL')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('s3-public.endpoint')
                            ->label('Endpoint')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                        Toggle::make('s3-public.usePathStyleEndpoint')
                            ->label('Use Path Style Endpoint')
                            ->default(false),
                        Toggle::make('s3-public.throw')
                            ->label('Throw')
                            ->default(false),
                        TextInput::make('s3-public.root')
                            ->label('Root')
                            ->string()
                            ->nullable()
                            ->maxLength(255),
                    ])->visible(app()->environment('local')),
                Toggle::make('Enabled')
                    ->default(true)
                    ->disabled()
                    ->afterStateHydrated(fn ($state, callable $set) => $set('Enabled', true)),
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

            $config->s3Filesystem = TenantS3FilesystemConfig::from(
                array_merge(
                    $config->s3Filesystem->toArray(),
                    $data['s3'],
                )
            );

            $config->s3PublicFilesystem = TenantS3FilesystemConfig::from(
                array_merge(
                    $config->s3PublicFilesystem->toArray(),
                    $data['s3-public'],
                )
            );

            $tenant->config = $config;

            $tenant->save();

            $this->callHook('afterSave');

            DB::commit();

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

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
