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

namespace AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages;

use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Filament\Blocks\CampaignActionBlock;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\CampaignResource;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Group\Models\Group;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\Width;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class CreateCampaign extends CreateRecord
{
    use HasWizard;

    protected ?bool $hasDatabaseTransactions = true;

    protected static string $resource = CampaignResource::class;

    public function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Great work on this campaign! 🎉')
            ->modalDescription('Before we create it, let us know how you’d like to proceed. Click the "Save as Draft" button if you want to save the campaign for further edits or choose the "Save and Enable" button to create and make it live immediately. If you’re not quite ready to create the campaign, simply select the "Cancel" button.')
            ->modalWidth(Width::ThreeExtraLarge)
            ->modalSubmitActionLabel('Save and Enable')
            ->extraModalFooterActions([
                Action::make('draft')
                    ->label('Save as Draft')
                    ->color('gray')
                    ->action(function () {
                        $state = $this->form->getRawState();
                        $state['enabled'] = false;
                        $this->form->fill($state, false, false);
                        $this->create();
                    })
                    ->cancelParentActions(),
            ])
            ->action(function () {
                $this->create();
            });
    }

    public function createAction(): Action
    {
        return $this->getCreateFormAction();
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Campaign Details')
                ->schema([
                    TextInput::make('name')
                        ->autocomplete(false)
                        ->required(),
                    Select::make('segment_id')
                        ->label('Population Group')
                        ->options(function () {
                            return Group::query()
                                ->whereHas('user', function ($query) {
                                    $query->whereKey(auth()->id())->orWhereRelation('team.users', 'id', auth()->id());
                                })
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                ]),
            Step::make('Define Journey')
                ->schema([
                    Builder::make('actions')
                        ->label(fn () => new HtmlString('<span class="text-xl">Journey Steps</span>'))
                        ->addActionLabel('Add a new Journey Step')
                        ->minItems(1)
                        ->blocks(CampaignActionType::blocks())
                        ->dehydrated(false)
                        ->validationAttribute('journey steps')
                        ->saveRelationshipsUsing(function (Builder $component, Campaign $record) {
                            $executeAt = null;

                            foreach ($component->getChildComponentContainers() as $item) {
                                /** @var CampaignActionBlock $block */
                                $block = $item->getParentComponent();

                                $itemData = $item->getState(shouldCallHooksBefore: false);

                                if (isset($itemData['execute_at']) && (($itemData['input_type'] ?? null) !== 'relative')) {
                                    $executeAt = $itemData['execute_at'];
                                } else {
                                    $executeAt = Carbon::parse($executeAt ?? null)->addDays($itemData['days'])->addHours($itemData['hours'])->addMinutes($itemData['minutes']);
                                    $itemData['execute_at'] = $executeAt;
                                }

                                $action = $record->actions()->create([
                                    'type' => $block->getName(),
                                    'data' => Arr::except($itemData, ['execute_at']),
                                    'execute_at' => $itemData['execute_at'],
                                ]);

                                $block->afterCreated($action, $item);

                                $item->model($action)->saveRelationships();
                            }
                        }),
                ]),
            Step::make('Review Campaign')
                ->schema([
                    Toggle::make('enabled')
                        ->default(true)
                        ->helperText('Toggle this off to set your campaign to a draft state.'),
                    ViewField::make('step-summary')
                        ->view('filament.forms.components.campaigns.step-summary'),
                ]),
        ];
    }
}
