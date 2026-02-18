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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\RelationManagers\CampaignActionsRelationManager;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use AdvisingApp\Group\Models\Group;
use App\Models\Tag;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group as ComponentsGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class TagsBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tags');

        $this->schema($this->createFields());
    }

    /**
     *
     * @param string $fieldPrefix
     *
     * @return array<Component>
     */
    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'tag_ids')
                ->label('Which tags should be applied?')
                ->options(function (Get $get, $livewire, string $operation) {
                    if ($livewire instanceof CreateCampaign) {
                        $groupId = $get('../../../segment_id');
                    } else {
                        $groupId = $livewire->getOwnerRecord()->segment_id;
                    }
                    $group = Group::find($groupId);

                    return Tag::where('type', $group->model)->orderBy('name', 'ASC')->pluck('name', 'id');
                })
                ->multiple()
                ->searchable()
                ->required()
                ->exists('tags', 'id'),
            Toggle::make($fieldPrefix . 'remove_prior')
                ->label('Remove all previously assigned tags?')
                ->default(false)
                ->hintIconTooltip('If checked, all prior tags assignments will be removed.'),
            ComponentsGroup::make()
                ->schema([
                    ToggleButtons::make('input_type')
                        ->label('How would you like to select when this step occurs?')
                        ->options([
                            'fixed' => 'Fixed Date',
                            'relative' => 'Relative Date',
                        ])
                        ->inline()
                        ->live()
                        ->visible(
                            fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) => array_key_first($get('../../')) !== explode('.', $component->getStatePath())[2] &&
                            $livewire instanceof CreateCampaign
                        )
                        ->required(),
                    DateTimePicker::make('execute_at')
                        ->label('When should the journey step be executed?')
                        ->visible(
                            fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) => ! ($livewire instanceof CreateCampaign) ||
                                    array_key_first($get('../../')) === explode('.', $component->getStatePath())[2] ||
                                    $get('input_type') === 'fixed'
                        )
                        ->columnSpanFull()
                        ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                        ->hintIconTooltip('This time is set in ' . app(CampaignSettings::class)->getActionExecutionTimezoneLabel() . '.')
                        ->lazy()
                        ->helperText(fn ($state): ?string => filled($state) ? $this->generateUserTimezoneHint(CarbonImmutable::parse($state)) : null)
                        ->required()
                        ->minDate(now()),
                    Section::make('How long after the previous step should this occur?')
                        ->schema([
                            TextInput::make('days')
                                ->translateLabel()
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('hours')
                                ->translateLabel()
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('minutes')
                                ->translateLabel()
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),
                        ])
                        ->visible(
                            fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) => array_key_first($get('../../')) !== explode('.', $component->getStatePath())[2] &&
                            $get('input_type') === 'relative' &&
                            $livewire instanceof CreateCampaign
                        )
                        ->columns(3),
                ]),
        ];
    }

    public static function type(): string
    {
        return 'tags';
    }
}
