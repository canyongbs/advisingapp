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

use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class ManageCategories extends EditRecord
{
    protected static string $resource = CustomerAdvisorResource::class;

    protected static ?string $title = 'Categories';

    protected static ?string $navigationLabel = 'Categories';

    protected static string | UnitEnum | null $navigationGroup = 'Configuration';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        return Gate::allows('viewAny', CustomerAdvisorCategory::class);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return Gate::allows('viewAny', CustomerAdvisorCategory::class);
    }

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
                Repeater::make('categories')
                    ->relationship()
                    ->hiddenLabel()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->table([
                        TableColumn::make('Name'),
                        TableColumn::make('Description'),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('description')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->extraItemActions([
                        Action::make('edit')
                            ->icon(Heroicon::PencilSquare)
                            ->modalHeading('Edit Customer Advisor Category')
                            ->slideOver()
                            ->modalWidth(Width::TwoExtraLarge)
                            ->authorize(fn (): bool => Gate::allows('update', CustomerAdvisorCategory::class))
                            ->fillForm(function (array $arguments): array {
                                /** @var CustomerAdvisor $customerAdvisor */
                                $customerAdvisor = $this->getRecord();

                                return $customerAdvisor
                                    ->categories()
                                    ->whereKey($this->getCategoryKeyFromItemArgument($arguments))
                                    ->firstOrFail()
                                    ->only(['name', 'description']);
                            })
                            ->schema(fn (array $arguments): array => $this->getCategoryFormComponents(ignoreId: $this->getCategoryKeyFromItemArgument($arguments)))
                            ->action(function (array $arguments, array $data): void {
                                /** @var CustomerAdvisor $customerAdvisor */
                                $customerAdvisor = $this->getRecord();

                                $customerAdvisor
                                    ->categories()
                                    ->whereKey($this->getCategoryKeyFromItemArgument($arguments))
                                    ->firstOrFail()
                                    ->update($data);

                                $this->refreshCategories();
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('New category')
                ->modalHeading('Create Customer Advisor Category')
                ->slideOver()
                ->modalWidth(Width::TwoExtraLarge)
                ->authorize(fn (): bool => Gate::allows('create', CustomerAdvisorCategory::class))
                ->schema($this->getCategoryFormComponents())
                ->action(function (array $data): void {
                    $this->getRecord()->categories()->create($data);

                    $this->refreshCategories();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * @return array<int, TextInput|Textarea>
     */
    protected function getCategoryFormComponents(?string $ignoreId = null): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->string()
                ->unique(
                    table: 'customer_advisor_categories',
                    column: 'name',
                    ignoreRecord: false,
                    modifyRuleUsing: function (Unique $rule) use ($ignoreId): void {
                        /** @var CustomerAdvisor $customerAdvisor */
                        $customerAdvisor = $this->getRecord();

                        $rule->where('customer_advisor_id', $customerAdvisor->getKey());

                        if (filled($ignoreId)) {
                            $rule->ignore($ignoreId);
                        }
                    }
                )
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('description')
                ->required()
                ->string()
                ->maxLength(65535)
                ->columnSpanFull(),
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function getCategoryKeyFromItemArgument(array $arguments): string
    {
        return Str::after($arguments['item'], 'record-');
    }

    protected function refreshCategories(): void
    {
        $this->getRecord()->refresh();

        $this->fillForm();
    }
}
