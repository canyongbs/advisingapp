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

namespace AdvisingApp\Prospect\Filament\Resources;

use Filament\Pages\Page;
use App\Features\PipelineFlag;
use Filament\Resources\Resource;
use AdvisingApp\Prospect\Models\Pipeline;
use AdvisingApp\Prospect\Settings\ProspectPipelineSettings;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\EditPipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ViewPipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ListPipelines;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\CreatePipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ManageEductables;

class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return parent::canAccess() && PipelineFlag::active() && app(ProspectPipelineSettings::class)->is_enabled;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPipeline::class,
            EditPipeline::class,
            ManageEductables::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPipelines::route('/'),
            'create' => CreatePipeline::route('/create'),
            'edit' => EditPipeline::route('/{record}/edit'),
            'view' => ViewPipeline::route('/{record}'),
            'manage' => ManageEductables::route('/manage/{record}'),
        ];
    }
}
