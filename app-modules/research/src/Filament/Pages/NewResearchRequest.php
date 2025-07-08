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

use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Research\Actions\GenerateResearchQuestion;
use AdvisingApp\Research\Jobs\Research;
use AdvisingApp\Research\Models\ResearchRequest;
use App\Enums\Feature;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * @property-read Form $form
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

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        if (! Gate::check(Feature::ResearchAdvisor->getGateName())) {
            return false;
        }

        if (blank(app(AiIntegrationsSettings::class)->jina_deepsearch_v1_api_key)) {
            return false;
        }

        return $user->can(['research_advisor.view-any', 'research_advisor.*.view']);
    }

    public function mount(): void
    {
        if ($this->researchRequest) {
            $this->form->fill([
                'topic' => $this->researchRequest->topic,
                'question_1' => $this->researchRequest->questions->get(0)?->response,
                'question_2' => $this->researchRequest->questions->get(1)?->response,
                'links' => $this->researchRequest->links,
            ]);
        } else {
            $this->form->fill();
        }
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            ManageResearchRequests::getUrl() => 'Research Advisor',
            'New',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->startOnStep($this->researchRequest?->hasStarted() ? 6 : ($this->researchRequest?->questions?->count() ?? 0) + 1)
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
                                if ($this->researchRequest?->hasStarted()) {
                                    return;
                                }

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

                                unset($this->researchRequest);

                                if (! $this->researchRequest) {
                                    return;
                                }

                                $this->researchRequest->questions()->create([
                                    'content' => app(GenerateResearchQuestion::class)->execute($this->researchRequest),
                                ]);
                                $this->researchRequest->load('questions');
                            })
                            ->disabled(fn (): bool => $this->researchRequest?->hasStarted() ?? false),
                        Step::make('Question 1')
                            ->schema([
                                Textarea::make('question_1')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(0)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                if ($this->researchRequest->hasStarted()) {
                                    return;
                                }

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
                            ->disabled(fn (): bool => $this->researchRequest?->hasStarted() ?? false),
                        Step::make('Question 2')
                            ->schema([
                                Textarea::make('question_2')
                                    ->label(fn (): ?string => $this->researchRequest?->questions->get(1)?->content)
                                    ->rows(5)
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->afterValidation(function (array $state) {
                                if ($this->researchRequest->hasStarted()) {
                                    return;
                                }

                                $this->researchRequest->questions->get(1)?->update([
                                    'response' => $state['question_2'],
                                ]);
                            })
                            ->disabled(fn (): bool => $this->researchRequest?->hasStarted() ?? false),
                        Step::make('Files')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('files')
                                    ->label('Please upload any institutional data or files you would like the research report to consider. This step is optional and you may click next if you don\'t have any files to upload.')
                                    ->collection('files')
                                    ->multiple()
                                    ->helperText('The maximum file size is 20MB.')
                                    ->maxSize(20000)
                                    ->maxFiles(5),
                            ])
                            ->afterValidation(function () {
                                if ($this->researchRequest->hasStarted()) {
                                    return;
                                }

                                $this->form->saveRelationships();
                            })
                            ->disabled(fn (): bool => $this->researchRequest?->hasStarted() ?? false),
                        Step::make('Links')
                            ->schema([
                                Repeater::make('links')
                                    ->label('During this research request, our agentic AI will research the internet and used advanced AI to answer your research question. If you would like us to review any specific links, you may add those here now. This step is optional and you may click next if you don\'t have any links to add.')
                                    ->simple(TextInput::make('url')
                                        ->required()
                                        ->url())
                                    ->reorderable(false)
                                    ->addActionLabel('Add link')
                                    ->maxItems(5),
                            ])
                            ->afterValidation(function (array $state) {
                                if ($this->researchRequest->hasStarted()) {
                                    return;
                                }

                                $this->researchRequest->update([
                                    'links' => $this->form->getState()['links'] ?? [],
                                ]);

                                dispatch(app(Research::class, [
                                    'researchRequest' => $this->researchRequest,
                                ]));
                            })
                            ->disabled(fn (): bool => $this->researchRequest?->hasStarted() ?? false),
                        Step::make('Results')
                            ->schema([
                                View::make('research::results')
                                    ->viewData(['researchRequest' => $this->researchRequest, 'showEmailResults' => false]),
                            ]),
                    ])
                    ->submitAction(filled($this->researchRequest?->title) ? Action::make('view')
                        ->label('Continue')
                        ->icon('heroicon-m-arrow-right')
                        ->iconPosition('after')
                        ->url(ManageResearchRequests::getUrl(['researchRequest' => $this->researchRequestId])) : null),
            ])
            ->model($this->researchRequest)
            ->statePath('data');
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
                ->label('New Request')
                ->requiresConfirmation()
                ->modalHeading('Restart this research request from scratch')
                ->color('gray')
                ->action(fn () => redirect(static::getUrl())),
        ];
    }
}
