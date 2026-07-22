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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Forms\Components\CampaignDateTimeInput;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InteractionBlock extends CampaignActionBlock
{
    /**
     * @var Model | array<string, mixed> | class-string<Model> | Closure | null
     */
    protected Model | array | string | Closure | null $model = Interaction::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Interaction');

        $this->schema($this->generateFields());
    }

    public function generateFields(): array
    {
        $settings = app(InteractionManagementSettings::class);

        return [
            Fieldset::make('Details')
                ->schema([
                    Select::make('interaction_initiative_id')
                        ->relationship('initiative', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->label('Initiative')
                        ->required(fn () => $settings->is_initiative_required)
                        ->visible(fn () => $settings->is_initiative_enabled)
                        ->exists((new InteractionInitiative())->getTable(), 'id'),
                    Select::make('interaction_driver_id')
                        ->relationship('driver', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Driver')
                        ->required(fn () => $settings->is_driver_required)
                        ->visible(fn () => $settings->is_driver_enabled)
                        ->exists((new InteractionDriver())->getTable(), 'id'),
                    Select::make('interaction_outcome_id')
                        ->relationship('outcome', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Outcome')
                        ->required(fn () => $settings->is_outcome_required)
                        ->visible(fn () => $settings->is_outcome_enabled)
                        ->exists((new InteractionOutcome())->getTable(), 'id'),
                    Select::make('interaction_relation_id')
                        ->relationship('relation', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Relation')
                        ->required(fn () => $settings->is_relation_required)
                        ->visible(fn () => $settings->is_relation_enabled)
                        ->exists((new InteractionRelation())->getTable(), 'id'),
                    Select::make('interaction_status_id')
                        ->relationship('status', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Status')
                        ->required(fn () => $settings->is_status_required)
                        ->visible(fn () => $settings->is_status_enabled)
                        ->exists((new InteractionStatus())->getTable(), 'id'),
                    Select::make('interaction_type_id')
                        ->relationship('type', 'name', $this->filterByInteractableType())
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Type')
                        ->required(fn () => $settings->is_type_required)
                        ->visible(fn () => $settings->is_type_enabled)
                        ->exists((new InteractionType())->getTable(), 'id'),
                ]),
            Fieldset::make('Time')
                ->schema([
                    DateTimePicker::make('start_datetime')
                        ->seconds(false)
                        ->required(),
                    DateTimePicker::make('end_datetime')
                        ->seconds(false)
                        ->required(),
                ]),
            Fieldset::make('Notes')
                ->schema([
                    TextInput::make('subject')
                        ->required(),
                    Textarea::make('description')
                        ->required(),
                ]),
            CampaignDateTimeInput::make(),
        ];
    }

    public static function type(): string
    {
        return 'interaction';
    }

    private function filterByInteractableType(): Closure
    {
        return function (Builder $query, Get $get, mixed $livewire) {
            $interactableType = $this->resolveInteractableType($get, $livewire);
            if (blank($interactableType)) {
                $query->whereRaw('1 = 0');
                return;
            }
            $query->where('interactable_type', $interactableType);
        };
    }

    private function resolveInteractableType(Get $get, mixed $livewire): ?InteractableType
    {
        $groupId = $this->resolveGroupId($get, $livewire);

        if (blank($groupId)) {
            return null;
        }

        $group = Group::query()->find($groupId);

        if (! $group) {
            return null;
        }

        return match ($group->model) {
            GroupModel::Student => InteractableType::Student,
            GroupModel::Prospect => InteractableType::Prospect,
        };
    }

    private function resolveGroupId(Get $get, mixed $livewire): mixed
    {
        if ($livewire instanceof CreateCampaign) {
            return $get('../../../segment_id');
        }

        if (method_exists($livewire, 'getOwnerRecord')) {
            return $livewire->getOwnerRecord()?->segment_id;
        }

        return null;
    }
}
