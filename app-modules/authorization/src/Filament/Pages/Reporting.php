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

namespace AdvisingApp\Authorization\Filament\Pages;

use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Models\ReportTeamAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\Report\Support\ReportAccess;
use AdvisingApp\Team\Models\Team;
use App\Enums\NavigationGroup;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use UnitEnum;

class Reporting extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected string $view = 'authorization::filament.pages.reporting';

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::UserManagement;

    protected static ?string $navigationLabel = 'Reporting';

    protected static ?string $title = 'Reporting';

    protected static ?int $navigationSort = 31;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        assert($user instanceof User);

        return $user->can('reporting.view-any');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (?string $search, ?array $filters): array {
                $categoryFilter = $filters['category']['value'] ?? null;

                return collect(ReportAccessKey::cases())
                    ->filter(fn (ReportAccessKey $key): bool => $key->isAvailableForTenant())
                    ->when(filled($search), function ($reports) use ($search) {
                        $needle = Str::lower($search);

                        return $reports->filter(
                            fn (ReportAccessKey $key): bool => Str::contains(Str::lower($key->getName()), $needle)
                                || Str::contains(Str::lower($key->getCategory()), $needle)
                        );
                    })
                    ->when(filled($categoryFilter), function ($reports) use ($categoryFilter) {
                        return $reports->filter(fn (ReportAccessKey $key): bool => $key->getCategory() === $categoryFilter);
                    })
                    ->mapWithKeys(fn (ReportAccessKey $key): array => [
                        $key->value => [
                            'report_key' => $key->value,
                            'name' => $key->getName(),
                            'category' => $key->getCategory(),
                            'access_count' => ReportAccess::accessCount($key),
                        ],
                    ])
                    ->all();
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Category')
                    ->searchable(),
                TextColumn::make('access_count')
                    ->label('Access Count')
                    ->numeric(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options($this->getCategoryOptions()),
            ])
            ->recordActions([
                $this->manageAction(),
            ])
            ->paginated(false);
    }

    public function manageAction(): Action
    {
        return Action::make('manage')
            ->label('Manage')
            ->icon('heroicon-m-user-group')
            ->slideOver()
            ->modalHeading(fn (array $record): string => "Manage Access: {$record['name']}")
            ->modalDescription('Grant access to this report by assigning individual users and/or teams.')
            ->fillForm(fn (array $record): array => [
                'users' => ReportUserAccess::query()
                    ->where('report_key', $record['report_key'])
                    ->pluck('user_id')
                    ->all(),
                'teams' => ReportTeamAccess::query()
                    ->where('report_key', $record['report_key'])
                    ->pluck('team_id')
                    ->all(),
            ])
            ->schema([
                Select::make('users')
                    ->label('Users')
                    ->multiple()
                    ->searchable(function ($query, $search) {
                        if (blank($search)) {
                            return $query;
                        }

                        return $query->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%');
                    })
                    ->preload()
                    ->options(fn (): array => User::query()
                        ->tap(new WithoutAnyAdmin())
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->toArray()),
                Select::make('teams')
                    ->label('Teams')
                    ->multiple()
                    ->searchable(function ($query, $search) {
                        if (blank($search)) {
                            return $query;
                        }

                        return $query->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%');
                    })
                    ->preload()
                    ->options(fn (): array => Team::query()
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->toArray()),
            ])
            ->action(function (array $record, array $data): void {
                $reportKey = $record['report_key'];

                $this->syncUserAccess($reportKey, $data['users'] ?? []);
                $this->syncTeamAccess($reportKey, $data['teams'] ?? []);

                Notification::make()
                    ->success()
                    ->title('Report access updated')
                    ->send();
            });
    }

    /**
     * @return array<string, string>
     */
    protected function getCategoryOptions(): array
    {
        return collect(ReportAccessKey::cases())
            ->filter(fn (ReportAccessKey $key): bool => $key->isAvailableForTenant())
            ->map(fn (ReportAccessKey $key): string => $key->getCategory())
            ->unique()
            ->sort()
            ->mapWithKeys(fn (string $category): array => [$category => $category])
            ->all();
    }

    /**
     * @param array<int, string> $userIds
     */
    protected function syncUserAccess(string $reportKey, array $userIds): void
    {
        ReportUserAccess::query()
            ->where('report_key', $reportKey)
            ->whereNotIn('user_id', $userIds)
            ->delete();

        $existing = ReportUserAccess::query()
            ->where('report_key', $reportKey)
            ->pluck('user_id')
            ->all();

        foreach (array_diff($userIds, $existing) as $userId) {
            ReportUserAccess::query()->create([
                'report_key' => $reportKey,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * @param array<int, string> $teamIds
     */
    protected function syncTeamAccess(string $reportKey, array $teamIds): void
    {
        ReportTeamAccess::query()
            ->where('report_key', $reportKey)
            ->whereNotIn('team_id', $teamIds)
            ->delete();

        $existing = ReportTeamAccess::query()
            ->where('report_key', $reportKey)
            ->pluck('team_id')
            ->all();

        foreach (array_diff($teamIds, $existing) as $teamId) {
            ReportTeamAccess::query()->create([
                'report_key' => $reportKey,
                'team_id' => $teamId,
            ]);
        }
    }
}
