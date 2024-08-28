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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Enums\Feature;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Form\Filament\Resources\FormResource;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ManageProspectApplicationSubmissions extends ManageRelatedRecords
{
    use ProspectHolisticViewPage;
    
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'applicationSubmissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Application Submissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Application Submissions';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && Gate::check(Feature::OnlineAdmissions->getGateName());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('submissible.name')
                    ->searchable()
                    ->url(fn (ApplicationSubmission $record): string => FormResource::getUrl('edit', ['record' => $record->submissible])),
                TextColumn::make('state')
                    ->badge()
                    ->translateLabel()
                    ->state(function (ApplicationSubmission $record) {
                        return $record->state->name;
                    })
                    ->color(function (ApplicationSubmission $record) {
                        return $record->state->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('application_submission_states', 'application_submissions.status_id', '=', 'application_submission_states.id')
                            ->orderBy('application_submission_states.name', $direction);
                    }),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (ApplicationSubmission $record) => "Submission Details: {$record->created_at}")
                    ->infolist(fn (ApplicationSubmission $record): array => [
                        TextEntry::make('state')
                            ->label('State')
                            ->badge()
                            ->state(function (ApplicationSubmission $record) {
                                return $record->state->name;
                            })
                            ->color(function (ApplicationSubmission $record) {
                                return $record->state->color->value;
                            }),
                        Section::make('Authenticated author')
                            ->schema([
                                TextEntry::make('author.' . $record->author::displayNameKey())
                                    ->label('Name'),
                                TextEntry::make('author.email')
                                    ->label('Email address'),
                            ])
                            ->columns(2),
                    ])
                    ->modalContent(
                        fn (ApplicationSubmission $record) => view('application::submission', ['submission' => $record])
                    ),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        /** @var Prospect $ownerRecord */
        $applicationSubmissionsCount = Cache::tags('application-submission-count')
            ->remember(
                "application-submission-count-{$ownerRecord->getKey()}",
                now()->addMinutes(5),
                function () use ($ownerRecord): int {
                    return $ownerRecord->applicationSubmissions()->count();
                },
            );

        $item->badge($applicationSubmissionsCount > 0 ? $applicationSubmissionsCount : null);

        return [$item];
    }
}
