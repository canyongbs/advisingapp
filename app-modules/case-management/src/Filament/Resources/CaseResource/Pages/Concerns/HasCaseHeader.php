<?php

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseResource\Pages\Concerns;

use AdvisingApp\CaseManagement\Filament\Resources\CaseResource\Pages\ViewCase;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Contracts\View\View;

trait HasCaseHeader
{
    public function getHeader(): ?View
    {
        $referrer = request()->query('referrer');
        
        if ($referrer === 'studentProfile' && $this->record->respondent instanceof Student) {
            $backButtonLabel = 'Back to student';
            $backButtonUrl = StudentResource::getUrl('view', ['record' => $this->record->respondent->getKey()]);
        } elseif ($referrer === 'prospectProfile' && $this->record->respondent instanceof Prospect) {
            $backButtonLabel = 'Back to prospect';
            $backButtonUrl = ProspectResource::getUrl('view', ['record' => $this->record->respondent->getKey()]);
        }

        return view('case-management::filament.resources.case-resource.view-case.header', [
            'heading' => 'View Case',
            'actions' => $this->getCachedHeaderActions(),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'backButtonLabel' => $backButtonLabel ?? null,
            'backButtonUrl' => $backButtonUrl ?? null ,
        ]);
    }

}
