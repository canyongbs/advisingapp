<?php

namespace AdvisingApp\CaseManagement\Livewire;

use AdvisingApp\CaseManagement\Models\CaseModel;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RenderCaseFeedbackForm extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $show = true;

    public CaseModel $case;

    public ?array $data = [];

    public function render(): View
    {
        return view('case-management::livewire.render-case-feedback-form')
            ->title(__('Case feedback for :case_no', ['case_no' => $this->case->case_number]));
    }
}
