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

namespace AdvisingApp\Interaction\Filament\Resources\InteractionOutcomeResource\Pages;

use AdvisingApp\Interaction\Filament\Resources\InteractionOutcomeResource;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use App\Features\InteractionMetadataFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property ComponentContainer $form
 */
class ListInteractionOutcomes extends ListRecords
{
    use InteractsWithForms;

    protected static string $resource = InteractionOutcomeResource::class;

    /** @var array<string, mixed> */
    public ?array $data = [];

    protected static string $view = 'interaction::filament.pages.list-interaction-outcomes';

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
            'is_outcome_enabled' => $settings->is_outcome_enabled,
            'is_outcome_required' => $settings->is_outcome_required,
        ];

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        if (! InteractionMetadataFeature::active()) {
            return $form->schema([]);
        }

        return $form
            ->schema([
                Section::make('Outcome Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_outcome_enabled')
                                    ->label('Enabled')
                                    ->live()
                                    ->afterStateUpdated(function (bool $state): void {
                                        $settings = $this->getSettings();
                                        $settings->is_outcome_enabled = $state;
                                        $settings->save();

                                        Notification::make()
                                            ->title('Outcome feature ' . ($state ? 'enabled' : 'disabled'))
                                            ->body(
                                                $state
                                                    ? 'You can now create, view, and manage outcomes across interactions.'
                                                    : 'Outcomes are hidden from all interactions (create, edit, view, and list).'
                                            )
                                            ->{ $state ? 'success' : 'warning' }()
                                            ->send();
                                    }),
                                Toggle::make('is_outcome_required')
                                    ->label('Required')
                                    ->live()
                                    ->visible(fn (Get $get) => $get('is_outcome_enabled'))
                                    ->afterStateUpdated(function (bool $state): void {
                                        $settings = $this->getSettings();
                                        $settings->is_outcome_required = $state;
                                        $settings->save();

                                        Notification::make()
                                            ->title('Outcome requirement ' . ($state ? 'enabled' : 'disabled'))
                                            ->body(
                                                $state
                                                ? 'Outcomes are now mandatory in all interactions (create, edit, view, and list).'
                                                : 'Outcomes are now optional in interactions.'
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
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('is_default')
                    ->label('Default')
                    ->query(fn (Builder $query) => $query->where('is_default', true)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
