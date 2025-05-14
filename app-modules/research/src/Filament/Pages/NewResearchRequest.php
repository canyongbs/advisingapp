<?php

namespace AdvisingApp\Research\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use AdvisingApp\Research\Jobs\Research;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Actions\GenerateResearch;
use AdvisingApp\Research\Actions\GenerateResearchTitle;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Research\Actions\GenerateResearchQuestion;

/**
 * @property-read bool $hasResearchStarted
 * @property-read ?ResearchRequest $researchRequest
 */
class NewResearchRequest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'research::filament.pages.new-research-request';

    #[Url]
    public ?string $researchRequestId = null;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = null;

    public function mount(): void
    {
        if ($this->researchRequest) {
            $this->form->fill([
                'topic' => $this->researchRequest->topic,
                'question_1' => $this->researchRequest->questions->get(0)?->response,
                'question_2' => $this->researchRequest->questions->get(1)?->response,
                'question_3' => $this->researchRequest->questions->get(2)?->response,
                'question_4' => $this->researchRequest->questions->get(3)?->response,
            ]);
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->startOnStep($this->hasResearchStarted ? 6 : ($this->researchRequest?->questions?->count() ?? 0) + 1)
                    ->steps([
                        Step::make('Topic')
                            ->schema([
                                Textarea::make('topic')
                                    ->label('Enter research topic')
                                    ->rows(5)
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

                                $this->researchRequest->questions()->create([
                                    'content' => app(GenerateResearchQuestion::class)->execute($this->researchRequest),
                                ]);
                                $this->researchRequest->load('questions');
                            })
                            ->disabled(fn (): bool => $this->hasResearchStarted),
                        Step::make('Question 1')
                            ->schema([
                                Textarea::make('question_1')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(0)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                $this->researchRequest->questions->get(0)?->update([
                                    'response' => $state['question_1'],
                                ]);

                                if (! $this->researchRequest->questions->get(1)) {
                                    $this->researchRequest->questions()->create([
                                        'content' => app(GenerateResearchQuestion::class)->execute($this->researchRequest),
                                    ]);
                                    $this->researchRequest->load('questions');
                                }
                            })
                            ->disabled(fn (): bool => $this->hasResearchStarted),
                        Step::make('Question 2')
                            ->schema([
                                Textarea::make('question_2')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(1)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                $this->researchRequest->questions->get(1)?->update([
                                    'response' => $state['question_2'],
                                ]);

                                if (! $this->researchRequest->questions->get(2)) {
                                    $this->researchRequest->questions()->create([
                                        'content' => app(GenerateResearchQuestion::class)->execute($this->researchRequest),
                                    ]);
                                    $this->researchRequest->load('questions');
                                }
                            })
                            ->disabled(fn (): bool => $this->hasResearchStarted),
                        Step::make('Question 3')
                            ->schema([
                                Textarea::make('question_3')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(2)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                $this->researchRequest->questions->get(2)?->update([
                                    'response' => $state['question_3'],
                                ]);

                                if (! $this->researchRequest->questions->get(3)) {
                                    $this->researchRequest->questions()->create([
                                        'content' => app(GenerateResearchQuestion::class)->execute($this->researchRequest),
                                    ]);
                                    $this->researchRequest->load('questions');
                                }
                            })
                            ->disabled(fn (): bool => $this->hasResearchStarted),
                        Step::make('Question 4')
                            ->schema([
                                Textarea::make('question_4')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(3)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                if ($this->hasResearchStarted) {
                                    return;
                                }

                                $this->researchRequest->questions->get(3)?->update([
                                    'response' => $state['question_4'],
                                ]);

                                dispatch(app(Research::class, [
                                    'researchRequest' => $this->researchRequest,
                                ]));
                            })
                            ->disabled(fn (): bool => $this->hasResearchStarted),
                        Step::make('Results')
                            ->schema([
                                View::make('research::results')
                                    ->viewData(['researchRequest' => $this->researchRequest]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    #[Computed]
    public function hasResearchStarted(): bool
    {
        return filled($this->researchRequest?->results) || filled($this->researchRequest?->questions->get(3)?->response);
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

    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array
    {
        return [
            Action::make('restart')
                ->label('New request')
                ->requiresConfirmation()
                ->modalHeading('Restart this research request from scratch')
                ->color('gray')
                ->action(fn () => redirect(static::getUrl())),
        ];
    }
}
