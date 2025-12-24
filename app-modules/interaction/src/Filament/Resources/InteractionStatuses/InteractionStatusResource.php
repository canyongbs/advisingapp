<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Filament\Resources\InteractionStatuses;

use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Enums\InteractionStatusColorOptions;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatuses\Pages\CreateInteractionStatus;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatuses\Pages\EditInteractionStatus;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatuses\Pages\ListInteractionStatuses;
use AdvisingApp\Interaction\Models\InteractionStatus;
use App\Features\InteractableTypeFeature;
use App\Filament\Clusters\InteractionManagement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class InteractionStatusResource extends Resource
{
    protected static ?string $model = InteractionStatus::class;

    protected static ?string $navigationLabel = 'Statuses';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = InteractionManagement::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Status Name')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => InteractableTypeFeature::active() ? $rule->where('interactable_type', $get('interactable_type')) : $rule
                    ),
                Select::make('color')
                    ->label('Color')
                    ->searchable()
                    ->options(InteractionStatusColorOptions::class)
                    ->required()
                    ->enum(InteractionStatusColorOptions::class),
                Select::make('interactable_type')
                    ->visible(InteractableTypeFeature::active())
                    ->label('Type')
                    ->required()
                    ->options(InteractableType::class)
                    ->enum(InteractableType::class),
                Toggle::make('is_default')
                    ->label('Default')
                    ->live()
                    ->hint(function (?InteractionStatus $record, $state): ?string {
                        $basicHint = InteractableTypeFeature::active() ? 'This will only affect interactions for the selected type.' : null;

                        if ($record?->is_default) {
                            return $basicHint;
                        }

                        if (! $state) {
                            return $basicHint;
                        }

                        $currentDefault = InteractionStatus::query()
                            ->where('is_default', true)
                            ->value('name');

                        if (blank($currentDefault)) {
                            return $basicHint;
                        }

                        return $basicHint . " The current default status is '{$currentDefault}', you are replacing it.";
                    })
                    ->hintColor('danger')
                    ->columnStart(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractionStatuses::route('/'),
            'create' => CreateInteractionStatus::route('/create'),
            'edit' => EditInteractionStatus::route('/{record}/edit'),
        ];
    }
}
