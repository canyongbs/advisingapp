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

namespace AdvisingApp\CaseManagement\Filament\Resources;

use AdvisingApp\CaseManagement\Filament\Resources\SlaResource\Pages\CreateSla;
use AdvisingApp\CaseManagement\Filament\Resources\SlaResource\Pages\EditSla;
use AdvisingApp\CaseManagement\Filament\Resources\SlaResource\Pages\ListSlas;
use AdvisingApp\CaseManagement\Filament\Resources\SlaResource\RelationManagers\CasePrioritiesRelationManager;
use AdvisingApp\CaseManagement\Models\Sla;
use App\Filament\Clusters\CaseManagementAdministration;
use App\Filament\Forms\Components\SecondsDurationInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class SlaResource extends Resource
{
    protected static ?string $model = Sla::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 60;

    protected static ?string $modelLabel = 'SLA';

    protected static ?string $pluralModelLabel = 'SLAs';

    protected static ?string $cluster = CaseManagementAdministration::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                SecondsDurationInput::make('response_seconds')
                    ->label('Response time'),
                SecondsDurationInput::make('resolution_seconds')
                    ->label('Resolution time'),
                Textarea::make('terms')
                    ->label('Terms and conditions')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function getRelations(): array
    {
        return [
            CasePrioritiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSlas::route('/'),
            'create' => CreateSla::route('/create'),
            'edit' => EditSla::route('/{record}/edit'),
        ];
    }
}
