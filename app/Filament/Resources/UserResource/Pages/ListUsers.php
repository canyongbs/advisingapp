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

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use App\Filament\Imports\UserImporter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Actions\BulkActionGroup;
use AdvisingApp\Authorization\Models\License;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Authorization\Enums\LicenseType;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;

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
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->toggleable(),
                TextColumn::make('job_title')
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
                    ->formatStateUsing(function ($state) {
                        // Return a custom value based on the record's attributes
                        return (! is_null($state)) ? 'Archived' : 'Active';
                    })
                    ->hidden(fn (Builder $query): bool => request()->get('tableFilters') ? (!request()->get('tableFilters')['trashed'] ?? true) : true),
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
                ]),
            ])
            ->filters([
                TrashedFilter::make()
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
