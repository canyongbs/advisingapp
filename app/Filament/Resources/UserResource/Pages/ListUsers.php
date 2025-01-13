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

namespace App\Filament\Resources\UserResource\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\License;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Team\Models\Team;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignRolesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignTeamBulkAction;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected ?string $heading = 'Product Users';

    public function getSubheading(): string | Htmlable | null
    {
        // TODO: Either remove or change to show all possible seats

        //return new HtmlString(view('crm-seats', [
        //    'count' => User::count(),
        //    'max' => app(LicenseSettings::class)->data->limits->crmSeats,
        //])->render());

        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('teams.name')
                    ->label('Team')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('job_title')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make(LicenseType::ConversationalAi->value . '_enabled')
                    ->label('AI Assistant')
                    ->state(fn (User $record): bool => $record->hasLicense(LicenseType::ConversationalAi))
                    ->boolean()
                    ->tooltip(fn (bool $state): string => $state ? 'Licensed' : 'Unlicensed')
                    ->toggleable(),
                IconColumn::make(LicenseType::RetentionCrm->value . '_enabled')
                    ->label('Retention')
                    ->state(fn (User $record): bool => $record->hasLicense(LicenseType::RetentionCrm))
                    ->boolean()
                    ->tooltip(fn (bool $state): string => $state ? 'Licensed' : 'Unlicensed')
                    ->toggleable(),
                IconColumn::make(LicenseType::RecruitmentCrm->value . '_enabled')
                    ->label('Recruitment')
                    ->state(fn (User $record): bool => $record->hasLicense(LicenseType::RecruitmentCrm))
                    ->boolean()
                    ->tooltip(fn (bool $state): string => $state ? 'Licensed' : 'Unlicensed')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Status')
                    ->getStateUsing(fn (User $record): string => $record->trashed() ? 'Archived' : 'Active')
                    ->visible(fn ($livewire) => isset($livewire->getTableFilterState('trashed')['value']) ? true : false),
            ])
            ->actions([
                Impersonate::make(),
                ViewAction::make(),
                EditAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    AssignLicensesBulkAction::make()
                        ->visible(fn () => auth()->user()->can('create', License::class)),
                    AssignRolesBulkAction::make()
                        ->visible(fn () => auth()->user()->can('user.*.update', User::class)),
                    AssignTeamBulkAction::make()
                        ->visible(fn () => auth()->user()->can('user.*.update', User::class)),
                ]),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible((fn () => auth()->user()->can('user.*.restore'))),
                SelectFilter::make('teams')
                    ->label('Team')
                    ->options(
                        fn (): array => [
                            '' => [
                                'unassigned' => 'Unassigned',
                            ],
                            'Teams' => Team::query()->take(50)->orderBy('name')->pluck('name', 'id')->toArray(),
                        ]
                    )
                    ->getSearchResultsUsing(fn (string $search): array => ['Teams' => Team::query()->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')->take(50)->pluck('name', 'id')->toArray()])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['values'])) {
                            return;
                        }

                        $query->when(in_array('unassigned', $data['values']), function ($query) {
                            $query->whereDoesntHave('teams');
                        })
                            ->{in_array('unassigned', $data['values']) ? 'orWhereHas' : 'whereHas'}('teams', function ($query) use ($data) {
                                $query->whereIn('team_id', array_filter($data['values'], fn ($value) => $value !== 'unassigned'));
                            });
                    })
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('roles')
                    ->label('Roles')
                    ->options(
                        fn (): array => [
                            '' => [
                                'none' => 'None',
                            ],
                            'Roles' => Role::query()->take(50)->orderBy('name')->pluck('name', 'id')->toArray(),
                        ]
                    )
                    ->getSearchResultsUsing(fn (string $search): array => ['Roles' => Role::query()->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')->take(50)->orderBy('name')->pluck('name', 'id')->toArray()])
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return;
                            }

                            $query->when(in_array('none', $data['values']), function ($query) {
                                $query->whereDoesntHave('roles');
                            })
                                ->{in_array('none', $data['values']) ? 'orWhereHas' : 'whereHas'}('roles', function ($query) use ($data) {
                                    $query->whereIn('id', array_filter($data['values'], fn ($value) => $value !== 'none'));
                                });
                        }
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('licenses')
                    ->label('License')
                    ->options(
                        fn (): array => [
                            '' => [
                                'no_assigned_license' => 'No Assigned License',
                            ],
                            'Licenses' => collect(LicenseType::cases())
                                ->mapWithKeys(fn ($case) => [$case->value => $case->name])
                                ->toArray(),
                        ]
                    )
                    ->getSearchResultsUsing(fn (string $search): array => ['Licenses' => collect(LicenseType::cases())->filter(fn ($case) => str_contains(strtolower($case->name), strtolower($search)))->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray()])
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return;
                            }

                            $query->when(in_array('no_assigned_license', $data['values']), function ($query) {
                                $query->whereDoesntHave('licenses');
                            })
                                ->{in_array('no_assigned_license', $data['values']) ? 'orWhereHas' : 'whereHas'}('licenses', function ($query) use ($data) {
                                    $query->whereIn('type', array_filter($data['values'], fn ($value) => $value !== 'no_assigned_license'));
                                });
                        }
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Filter::make('created_after')
                    ->form([
                        DateTimePicker::make('created_at')
                        ->label('Created After')
                        ->format('m/d/Y H:i:s')
                        ->displayFormat('m/d/Y H:i:s')
                        ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_at'],
                                fn (Builder $query, $date): Builder => $query->where('created_at', '>=', Carbon::parse($date, app(DisplaySettings::class)->getTimezone() ?? config('app.timezone'))->setTimezone('UTC')),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_at']) {
                            return null;
                        }
                 
                        return 'Created After ' . Carbon::parse($data['created_at'], app(DisplaySettings::class)->getTimezone() ?? config('app.timezone'))->setTimezone('UTC')->format('m/d/Y H:i:s');
                    }),
            ])
            ->defaultSort('name', 'asc');
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(UserImporter::class)
                ->authorize('import', User::class),
            CreateAction::make(),
        ];
    }
}
