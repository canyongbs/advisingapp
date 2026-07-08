<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Settings\AiCustomerAdvisorSettings;
use App\Filament\Forms\Components\AvatarUploadOrAiGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use UnitEnum;

class EditCustomerAdvisor extends EditRecord
{
    protected static string $resource = CustomerAdvisorResource::class;

    protected static ?string $navigationLabel = 'Edit';

    protected static string | UnitEnum | null $navigationGroup = 'Customer Advisor';

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var CustomerAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    AvatarUploadOrAiGenerator::make(),
                    Grid::make()->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255),
                        Select::make('model')
                            ->live()
                            ->options(fn (AiModel|string|null $state) => array_unique([
                                ...AiModelApplicabilityFeature::CustomerAdvisor->getModelsAsSelectOptions(),
                                ...match (true) {
                                    $state instanceof AiModel => [$state->value => $state->getLabel()],
                                    is_string($state) => [$state => AiModel::parse($state)->getLabel()],
                                    default => [],
                                },
                            ]))
                            ->searchable()
                            ->required()
                            ->visible(auth()->user()->isSuperAdmin())
                            ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::CustomerAdvisor->getModels()))
                            ->disabled(fn (): bool => ! app(AiCustomerAdvisorSettings::class)->allow_selection_of_model)
                            ->default(function () {
                                $settings = app(AiCustomerAdvisorSettings::class);

                                if ($settings->allow_selection_of_model) {
                                    return null;
                                }

                                return $settings->preselected_model;
                            }),
                    ])
                        ->columns(2),
                    Textarea::make('description')
                        ->maxLength(65535)
                        ->required(),
                    Toggle::make('is_introductory_message_enabled')
                        ->label('Enable Introductory Message')
                        ->live()
                        ->default(false),
                    Toggle::make('is_introductory_message_dynamic')
                        ->label('Dynamic')
                        ->helperText(fn (Get $get): string => $get('is_introductory_message_dynamic')
                            ? 'AI will greet the student or prospect.'
                            : 'Specify a custom introductory message below.')
                        ->live()
                        ->default(true)
                        ->visible(fn (Get $get): bool => $get('is_introductory_message_enabled')),
                    Textarea::make('introductory_message')
                        ->label('Introductory Message')
                        ->helperText('Specify the plain text introductory message.')
                        ->maxLength(65535)
                        ->rows(4)
                        ->visible(fn (Get $get): bool => $get('is_introductory_message_enabled') && ! $get('is_introductory_message_dynamic')),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $settings = app(AiCustomerAdvisorSettings::class);

        if (! $settings->allow_selection_of_model && $settings->preselected_model) {
            $data['model'] = $settings->preselected_model;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->authorize(fn (): bool => auth()->user()->can('customer_advisor.*.delete'))
                ->color('danger')
                ->action(function () {
                    /** @var CustomerAdvisor $customerAdvisor */
                    $customerAdvisor = $this->getRecord();
                    $customerAdvisor->archived_at = now();
                    $customerAdvisor->save();

                    Notification::make()
                        ->title('Customer Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (CustomerAdvisor $record): bool => (bool) $record->archived_at),
            Action::make('restore')
                ->authorize(fn (): bool => auth()->user()->can('customer_advisor.*.restore'))
                ->action(function () {
                    /** @var CustomerAdvisor $customerAdvisor */
                    $customerAdvisor = $this->getRecord();
                    $customerAdvisor->archived_at = null;
                    $customerAdvisor->save();

                    Notification::make()
                        ->title('Customer Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (CustomerAdvisor $record): bool {
                    if (! $record->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
