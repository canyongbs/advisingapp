<?php

namespace AdvisingApp\Research\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Textarea;
use App\Models\User;
use Filament\Forms\Components\Wizard\Step;
use AdvisingApp\Research\Models\ResearchRequest;

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
