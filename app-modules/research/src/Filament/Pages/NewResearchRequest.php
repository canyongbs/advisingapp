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

namespace AdvisingApp\Research\Filament\Pages;

use AdvisingApp\Research\Models\ResearchRequest;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * @property-read ?ResearchRequest $researchRequest
 */
class NewResearchRequest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'research::filament.pages.new-research-request';

    #[Url]
    public ?string $researchRequestId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->steps([
                        Step::make('Topic')
                            ->schema([
                                Textarea::make('topic')
                                    ->label('Enter research topic')
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                if ($this->researchRequest) {
                                    $this->researchRequest->update([
                                        'topic' => $state['topic'],
                                    ]);

                                    return;
                                }

                                /** @var User $user */
                                $user = auth()->user();

                                $researchRequest = new ResearchRequest();
                                $researchRequest->topic = $state['topic'];
                                $researchRequest->user()->associate($user);
                                $researchRequest->save();

                                $this->researchRequestId = $researchRequest->getKey();
                            }),
                        Step::make('Question 1'),
                        Step::make('Question 2'),
                        Step::make('Question 3'),
                        Step::make('Question 4'),
                        Step::make('Results'),
                    ]),
            ]);
    }

    #[Computed]
    public function researchRequest(): ?ResearchRequest
    {
        if (blank($this->researchRequestId)) {
            return null;
        }

        /** @var User $user */
        $user = auth()->user();

        return $user->researchRequests()->find($this->researchRequestId);
    }
}
