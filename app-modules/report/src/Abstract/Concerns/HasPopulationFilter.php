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

namespace AdvisingApp\Report\Abstract\Concerns;

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Abstract\Contracts\HasGroupModel;
use AdvisingApp\Report\Filament\Forms\Components\LiveFilterBuilder;
use AdvisingApp\Report\Filament\Tables\ReportGroupsTable;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\JoinMatchedTerm;
use Filament\Actions\Action;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Url;

/**
 * Adds the "Advanced Filtering" population selector to a report page.
 *
 * A report may filter its population either by a saved {@see Group} ("Saved group") or by an
 * ad-hoc "Live filter" built with the exact same experience as the Group builder. Both are
 * chosen from a slide-over: the saved group via a {@see TableSelect}, and the live filter via a
 * {@see LiveFilterBuilder} — both of which own their own nested table components, so the page
 * itself does not need to be a table. The live filter is ephemeral (persisted in the URL so it
 * survives refreshes) and is never written to the database unless the user chooses "Save as group".
 */
trait HasPopulationFilter
{
    /**
     * The currently applied population selection.
     *
     * Kept as a single URL-persisted property (rather than one param per field) so that switching
     * between a saved group and a live filter atomically replaces the whole value — Livewire's
     * `#[Url]` handling does not reliably clear a stale string param when it is set back to null.
     *
     * Shape: `['type' => 'saved'|'live', 'groupId' => ?string, 'liveFilters' => ?array]`.
     *
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $population = null;

    /**
     * Provided by {@see HasGroupModel}.
     */
    abstract public function groupModel(): ?GroupModel;

    /**
     * A live filter in the URL can predate changes to the group filters, which would otherwise be skipped and widen the population,
     * so known legacy rules are converted, and a live filter that still uses a filter that no longer exists is cleared.
     */
    public function mountHasPopulationFilter(): void
    {
        if (($this->getPopulationType() !== 'live') || (! $this->hasLiveFilters($this->getSelectedLiveFilters()))) {
            return;
        }

        $liveFilters = $this->migrateLegacyLiveFilters($this->getSelectedLiveFilters());

        if (! $this->hasLiveFilters($liveFilters)) {
            return;
        }

        $availableTypes = array_map(
            fn (Constraint $constraint): string => $constraint->getName(),
            $this->getPopulationGroupModel()->queryBuilderConstraints(),
        );

        if (filled(array_diff($this->getRuleTypes($liveFilters['queryBuilder']['rules']), $availableTypes))) {
            $this->population = null;

            Notification::make()
                ->title('Live filter removed')
                ->body('This report\'s live filter uses filters that are no longer available, so it has been removed. Please build the live filter again.')
                ->warning()
                ->send();

            return;
        }

        $this->population['liveFilters'] = $liveFilters;
    }

    /**
     * The payload handed to widgets (via `pageFilters`) so they can apply the population filter.
     *
     * @return array{type: ?string, groupId: ?string, liveFilters: ?array<string, mixed>, model: ?string}
     */
    public function getPopulationPayload(): array
    {
        return [
            'type' => $this->getPopulationType(),
            'groupId' => $this->getSelectedGroupId(),
            'liveFilters' => $this->getSelectedLiveFilters(),
            'model' => $this->getPopulationGroupModel()->value,
        ];
    }

    public function getPopulationFilterSection(): Section
    {
        return Section::make()
            ->heading('Advanced Filtering')
            ->schema([
                Text::make(fn (): string => $this->getPopulationSummary()),
                Actions::make([
                    $this->getSelectSavedGroupAction(),
                    $this->getBuildLiveFilterAction(),
                    $this->getSaveAsGroupAction(),
                    $this->getClearPopulationAction(),
                ]),
            ])
            ->columns(1);
    }

    /**
     * Converts legacy live-filter rule types and values to the current equivalent format.
     *
     * @param array<string, mixed>|null $liveFilters
     *
     * @return array<string, mixed>|null
     */
    protected function migrateLegacyLiveFilters(?array $liveFilters): ?array
    {
        if (! is_array($liveFilters) || ! is_array($liveFilters['queryBuilder']['rules'] ?? null)) {
            return $liveFilters;
        }

        $liveFilters['queryBuilder']['rules'] = $this->migrateLegacyRules($liveFilters['queryBuilder']['rules']);

        return $liveFilters;
    }

    /**
     * @param array<array-key, mixed> $rules
     *
     * @return array<array-key, mixed>
     */
    protected function migrateLegacyRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if (! is_array($rule)) {
                continue;
            }

            if (($rule['type'] ?? null) === 'or') {
                foreach (($rule['data']['groups'] ?? []) as $groupKey => $group) {
                    if (! is_array($group) || ! is_array($group['rules'] ?? null)) {
                        continue;
                    }

                    $rules[$key]['data']['groups'][$groupKey]['rules'] = $this->migrateLegacyRules($group['rules']);
                }

                continue;
            }

            $legacyType = $rule['type'] ?? null;

            $newType = match ($legacyType) {
                'firstEnrollmentTerm.semester_name' => 'firstEnrolledTerm',
                'mostRecentEnrollmentTerm.semester_name' => 'mostRecentEnrolledTerm',
                default => null,
            };

            if (! is_string($newType)) {
                continue;
            }

            $values = $rule['data']['settings']['values'] ?? null;
            $rules[$key]['type'] = $newType;
            $rules[$key]['data']['settings']['values'] = is_array($values)
                ? $this->legacyEnrolledTermValuesToMatchedTermNames($values)
                : $values;
        }

        return $rules;
    }

    /**
     * @param array<array-key, mixed> $values
     *
     * @return array<array-key, mixed>
     */
    protected function legacyEnrolledTermValuesToMatchedTermNames(array $values): array
    {
        $semesterNames = collect($values)
            ->filter(fn (mixed $value): bool => is_string($value))
            ->map(fn (string $value): string => mb_strtolower($value))
            ->unique()
            ->values();

        if ($semesterNames->isEmpty()) {
            return $values;
        }

        $mappedTermNames = Enrollment::query()
            ->tap(new JoinMatchedTerm())
            ->whereNotNull('enrollments.semester_name')
            ->whereIn(DB::raw('enrollments.semester_name::citext'), $semesterNames->all())
            ->selectRaw('lower(enrollments.semester_name) as legacy_semester_name, lower(terms.name) as matched_term_name')
            ->distinct()
            ->pluck('matched_term_name', 'legacy_semester_name')
            ->all();

        return collect($values)
            ->map(function (mixed $value) use ($mappedTermNames): mixed {
                if (! is_string($value)) {
                    return $value;
                }

                $legacyName = mb_strtolower($value);

                return $mappedTermNames[$legacyName] ?? $value;
            })
            ->all();
    }

    protected function getPopulationGroupModel(): GroupModel
    {
        return $this->groupModel() ?? GroupModel::default();
    }

    protected function getPopulationType(): ?string
    {
        return $this->population['type'] ?? null;
    }

    protected function getSelectedGroupId(): ?string
    {
        return $this->population['groupId'] ?? null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getSelectedLiveFilters(): ?array
    {
        return $this->population['liveFilters'] ?? null;
    }

    /**
     * Determine whether a set of filters contains any active QueryBuilder rules.
     *
     * @param array<string, mixed>|null $filters
     */
    protected function hasLiveFilters(?array $filters): bool
    {
        $rules = data_get($filters, 'queryBuilder.rules');

        return is_array($rules) && filled($rules);
    }

    /**
     * The types of the rules, including rules nested inside "or" blocks.
     *
     * @param array<array-key, mixed> $rules
     *
     * @return array<string>
     */
    protected function getRuleTypes(array $rules): array
    {
        $types = [];

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $type = $rule['type'] ?? null;

            if ($type !== 'or') {
                $types[] = is_string($type) ? $type : '';

                continue;
            }

            foreach ($rule['data']['groups'] ?? [] as $orGroup) {
                if (is_array($orGroup['rules'] ?? null)) {
                    $types = [...$types, ...$this->getRuleTypes($orGroup['rules'])];
                }
            }
        }

        return array_values(array_unique($types));
    }

    protected function getSelectSavedGroupAction(): Action
    {
        return Action::make('selectSavedGroup')
            ->label(fn (): string => $this->getPopulationType() === 'saved' ? 'Change saved group' : 'Saved group')
            ->icon(Heroicon::Bookmark)
            ->slideOver()
            ->modalWidth(Width::FiveExtraLarge)
            ->modalHeading('Saved group')
            ->modalDescription('Filter this report to the members of a saved group.')
            ->modalSubmitActionLabel('Apply')
            ->fillForm(fn (): array => [
                'groupId' => $this->getPopulationType() === 'saved' ? $this->getSelectedGroupId() : null,
            ])
            ->schema([
                TableSelect::make('groupId')
                    ->hiddenLabel()
                    ->tableConfiguration(ReportGroupsTable::class)
                    ->tableArguments(fn (): array => ['model' => $this->getPopulationGroupModel()->value]),
            ])
            ->action(function (array $data): void {
                $groupId = $data['groupId'] ?? null;

                $this->population = filled($groupId)
                    ? ['type' => 'saved', 'groupId' => $groupId]
                    : null;
            });
    }

    protected function getBuildLiveFilterAction(): Action
    {
        return Action::make('buildLiveFilter')
            ->label(fn (): string => $this->getPopulationType() === 'live' ? 'Edit live filter' : 'Live filter')
            ->icon(Heroicon::Funnel)
            ->slideOver()
            ->modalWidth(Width::SevenExtraLarge)
            ->modalHeading('Live filter')
            ->modalDescription('Build a live filter just for this report.')
            ->modalSubmitActionLabel('Apply')
            ->fillForm(fn (): array => [
                'liveFilters' => $this->getPopulationType() === 'live' ? $this->getSelectedLiveFilters() : null,
            ])
            ->schema([
                LiveFilterBuilder::make('liveFilters')
                    ->hiddenLabel()
                    ->groupModel(fn (): GroupModel => $this->getPopulationGroupModel()),
            ])
            ->action(function (array $data): void {
                $filters = $data['liveFilters'] ?? [];

                $this->population = $this->hasLiveFilters($filters)
                    ? ['type' => 'live', 'liveFilters' => $filters]
                    : null;
            });
    }

    protected function getSaveAsGroupAction(): Action
    {
        return Action::make('saveAsGroup')
            ->label('Save as group')
            ->icon(Heroicon::Bookmark)
            ->color('gray')
            ->visible(fn (): bool => $this->getPopulationType() === 'live'
                && filled($this->getSelectedLiveFilters())
                && Gate::allows('create', Group::class))
            ->modalHeading('Save as group')
            ->modalDescription('Save this live filter as a reusable group.')
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Save group')
            ->schema([
                TextInput::make('name')
                    ->label('Group name')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: 'segments',
                        column: 'name',
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->withoutTrashed(),
                    ),
            ])
            ->action(function (array $data): void {
                $group = new Group();
                $group->name = $data['name'];
                $group->model = $this->getPopulationGroupModel();
                $group->type = GroupType::Dynamic;
                $group->filters = $this->getSelectedLiveFilters() ?? [];
                $group->save();

                $this->population = ['type' => 'saved', 'groupId' => $group->getKey()];

                Notification::make()
                    ->title('Group saved')
                    ->body("Your live filter has been saved as the group \"{$group->name}\".")
                    ->success()
                    ->send();
            });
    }

    protected function getClearPopulationAction(): Action
    {
        return Action::make('clearPopulation')
            ->label('Clear')
            ->icon(Heroicon::XMark)
            ->color('gray')
            ->link()
            ->visible(fn (): bool => filled($this->population))
            ->action(function (): void {
                $this->population = null;
            });
    }

    protected function getPopulationSummary(): string
    {
        return match ($this->getPopulationType()) {
            'saved' => 'Filtering by saved group: ' . ($this->getPopulationGroupName() ?? 'Unknown group'),
            'live' => 'Filtering by a live filter.',
            default => 'No filter applied. This report includes all records.',
        };
    }

    protected function getPopulationGroupName(): ?string
    {
        $groupId = $this->getSelectedGroupId();

        if (blank($groupId)) {
            return null;
        }

        return Group::query()
            ->whereKey($groupId)
            ->value('name');
    }
}
