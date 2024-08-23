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
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Concerns\FiltersManagersFromGroups;
use Filament\Resources\RelationManagers\RelationGroup;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\RelationManagers\AssetCheckInRelationManager;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\RelationManagers\AssetCheckOutRelationManager;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\RelationManagers\ServiceRequestsRelationManager;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ProspectServiceManagement extends ManageRelatedRecords
{
    use FiltersManagersFromGroups;

    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'serviceRequests';

    protected static ?string $navigationLabel = 'Service Management';

    protected static ?string $breadcrumb = 'Service Management';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public function getTitle(): string | Htmlable
    {
        return 'Prospect Service Management';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && Gate::check(Feature::ServiceManagement->getGateName()) && count(static::managers($parameters['record'] ?? null));
    }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(?Model $record = null): array
    {
        return collect([
            ServiceRequestsRelationManager::class,
            RelationGroup::make('Assets', [
                AssetCheckOutRelationManager::class,
                AssetCheckInRelationManager::class,
            ]),
        ])
            ->map(fn ($relationManager) => self::filterRelationManagers($relationManager, $record))
            ->filter()
            ->toArray();
    }

    public function boot()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn (): View => view('prospect::student-converted-badge')
        );
    }
}
