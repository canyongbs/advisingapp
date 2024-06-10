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

namespace AdvisingApp\Report\Filament\Resources\ReportResource\Pages;

use App\Models\User;
use Filament\Forms\Set;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Enums\ReportModel;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\CheckboxList;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Concerns\InteractsWithTable;
use AdvisingApp\Report\Filament\Resources\ReportResource;

class CreateReport extends CreateRecord implements HasTable
{
    use InteractsWithTable;
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ReportResource::class;

    public function getSteps(): array
    {
        $models = $this->getReportModels();

        return [
            Step::make('Characteristics')
                ->schema([
                    TextInput::make('name')
                        ->autocomplete(false)
                        ->string()
                        ->required(),
                    Textarea::make('description'),
                ]),
            Step::make('Report Type')
                ->schema([
                    Select::make('model')
                        ->options(array_reduce($models, fn (array $options, ReportModel $model): array => [
                            ...$options,
                            $model->value => $model->getLabel(),
                        ], []))
                        ->required()
                        ->default(Arr::first($models))
                        ->selectablePlaceholder(false)
                        ->afterStateUpdated(function (Set $set) {
                            $set('columns', collect($this->getReportModel()->exporter()::getColumns())
                                ->filter(fn (ExportColumn $column): bool => $column->isEnabledByDefault())
                                ->map(fn (ExportColumn $column): string => $column->getName())
                                ->values()
                                ->all());

                            $this->cacheForms();
                            $this->bootedInteractsWithTable();
                            $this->resetTableFiltersForm();
                        }),
                ])
                ->columns(2)
                ->visible(count($models) > 1),
            Step::make('Create Custom Report')
                ->schema([
                    CheckboxList::make('columns')
                        ->options(fn (): array => array_reduce(
                            $this->getReportModel()->exporter()::getColumns(),
                            fn (array $options, ExportColumn $column): array => [
                                ...$options,
                                $column->getName() => $column->getLabel(),
                            ],
                            [],
                        ))
                        ->default(fn (): array => collect($this->getReportModel()->exporter()::getColumns())
                            ->filter(fn (ExportColumn $column): bool => $column->isEnabledByDefault())
                            ->map(fn (ExportColumn $column): string => $column->getName())
                            ->values()
                            ->all())
                        ->columns(3)
                        ->live()
                        ->afterStateUpdated($this->bootedInteractsWithTable(...)),
                    View::make('filament.forms.components.table'),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        $model = $this->getReportModel();
        $columns = $this->form->getRawState()['columns'] ?? [];

        return $model->table($table)
            ->columns(array_reduce(
                $model->exporter()::getColumns(type: TextColumn::class),
                fn (array $carry, TextColumn $column): array => [
                    ...$carry,
                    ...(in_array($column->getName(), $columns) ? [$column->getName() => $column] : []),
                ],
                [],
            ));
    }

    protected function getReportModels(): array
    {
        return [
            ...(auth()->user()->hasLicense(Student::getLicenseType()) ? [ReportModel::Student] : []),
            ...(auth()->user()->hasLicense(Prospect::getLicenseType()) ? [ReportModel::Prospect] : []),
            ...(auth()->user()->can('viewAny', User::class) ? [ReportModel::User] : []),
        ];
    }

    protected function getReportModel(): ReportModel
    {
        $model = $this->form->getRawState()['model'] ?? null;
        $models = $this->getReportModels();

        if (filled($model) && in_array(ReportModel::tryFromCaseOrValue($model), $models)) {
            return $model;
        }

        return Arr::first($models);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['model'] = $this->getReportModel();
        $data['filters'] = $this->tableFilters ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        $exporter = $this->getReportModel()->exporter();
        $columns = $this->form->getRawState()['columns'] ?? [];

        ExportAction::make()
            ->livewire($this)
            ->exporter($exporter)
            ->modalHidden()
            ->formData([
                'columnMap' => array_reduce($exporter::getColumns(), fn (array $carry, ExportColumn $column): array => [
                    ...$carry,
                    $column->getName() => [
                        'isEnabled' => in_array($column->getName(), $columns),
                        'label' => $column->getLabel(),
                    ],
                ], []),
            ])
            ->call();
    }
}
