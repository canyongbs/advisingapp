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

namespace AdvisingApp\Interaction\Filament\Resources\InteractionStatusResource\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatusResource;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use App\Features\InteractionMetadataFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property \Filament\Schemas\Schema $form
 */
class ListInteractionStatuses extends ListRecords
{
    use InteractsWithForms;

    protected static string $resource = InteractionStatusResource::class;

    /** @var array<string, mixed> */
    public ?array $data = [];

    protected string $view = 'interaction::filament.pages.list-interaction-statuses';

    private ?InteractionManagementSettings $settings = null;

    public function mount(): void
    {
        if (InteractionMetadataFeature::active()) {
            $this->fillForm();
        }
    }

    public function fillForm(): void
    {
        $settings = $this->getSettings();

        $data = [
            'is_status_enabled' => $settings->is_status_enabled,
            'is_status_required' => $settings->is_status_required,
        ];

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        if (! InteractionMetadataFeature::active()) {
            return $schema->components([]);
        }

        return $schema
            ->components([
                Section::make('Status Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_status_enabled')
                                    ->label('Enabled')
                                    ->live()
                                    ->afterStateUpdated(function (bool $state): void {
                                        $settings = $this->getSettings();
                                        $settings->is_status_enabled = $state;
                                        $settings->save();

                                        Notification::make()
                                            ->title('Status settings updated')
                                            ->body($state ? 'Status functionality has been enabled.' : 'Status functionality has been disabled.')
                                            ->success()
                                            ->send();

                                        Notification::make()
                                            ->title('Status feature ' . ($state ? 'enabled' : 'disabled'))
                                            ->body(
                                                $state
                                                    ? 'You can now create, view, and manage statuses across interactions.'
                                                    : 'Statuses are hidden from all interactions (create, edit, view, and list).'
                                            )
                                            ->{ $state ? 'success' : 'warning' }()
                                            ->send();
                                    }),
                                Toggle::make('is_status_required')
                                    ->label('Required')
                                    ->live()
                                    ->visible(fn (Get $get) => $get('is_status_enabled'))
                                    ->afterStateUpdated(function (bool $state): void {
                                        $settings = $this->getSettings();
                                        $settings->is_status_required = $state;
                                        $settings->save();
                                        Notification::make()
                                            ->title('Status requirement ' . ($state ? 'enabled' : 'disabled'))
                                            ->body(
                                                $state
                                                ? 'Statuses are now mandatory in all interactions (create, edit, view, and list).'
                                                : 'Statuses are now optional in interactions.'
                                            )
                                            ->{ $state ? 'success' : 'info' }()
                                            ->send();
                                    }),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (InteractionStatus $interactionStatus) => $interactionStatus->color->value),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('is_default')
                    ->label('Default')
                    ->query(fn (Builder $query) => $query->where('is_default', true)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    private function getSettings(): InteractionManagementSettings
    {
        if ($this->settings === null) {
            $this->settings = app(InteractionManagementSettings::class);
        }

        return $this->settings;
    }
}
