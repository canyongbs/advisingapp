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

namespace AdvisingApp\Interaction\Filament\Resources;

use AdvisingApp\Interaction\Filament\Resources\InteractionRelationResource\Pages\CreateInteractionRelation;
use AdvisingApp\Interaction\Filament\Resources\InteractionRelationResource\Pages\EditInteractionRelation;
use AdvisingApp\Interaction\Filament\Resources\InteractionRelationResource\Pages\ListInteractionRelations;
use AdvisingApp\Interaction\Models\InteractionRelation;
use App\Filament\Clusters\InteractionManagement;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class InteractionRelationResource extends Resource
{
    protected static ?string $model = InteractionRelation::class;

    protected static ?string $navigationLabel = 'Relations';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = InteractionManagement::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Relation Name'),
                Toggle::make('is_default')
                    ->label('Default')
                    ->live()
                    ->hint(function (?InteractionRelation $record, $state): ?string {
                        if ($record?->is_default) {
                            return null;
                        }

                        if (! $state) {
                            return null;
                        }

                        $currentDefault = InteractionRelation::query()
                            ->where('is_default', true)
                            ->value('name');

                        if (blank($currentDefault)) {
                            return null;
                        }

                        return "The current default relation is '{$currentDefault}', you are replacing it.";
                    })
                    ->hintColor('danger')
                    ->columnStart(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractionRelations::route('/'),
            'create' => CreateInteractionRelation::route('/create'),
            'edit' => EditInteractionRelation::route('/{record}/edit'),
        ];
    }
}
