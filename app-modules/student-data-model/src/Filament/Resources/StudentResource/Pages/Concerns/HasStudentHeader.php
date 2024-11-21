<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns;

use App\Settings\DisplaySettings;
use Illuminate\Contracts\View\View;
use AdvisingApp\Notification\Filament\Actions\SubscribeHeaderAction;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Actions\SyncStudentSisAction;

trait HasStudentHeader
{
    public function getHeader(): ?View
    {
        $sisSettings = app(StudentInformationSystemSettings::class);

        $student = $this->getRecord();
        $studentName = filled($student->full_name)
            ? $student->full_name
            : "{$student->first} {$student->last}";

        return view('student-data-model::filament.resources.educatable-resource.view-educatable.header', [
            'actions' => $this->getCachedHeaderActions(),
            'backButtonLabel' => 'Back to student',
            'backButtonUrl' => $this instanceof ViewStudent
                ? null
                : StudentResource::getUrl('view', ['record' => $this->getRecord()]),
            'badges' => [
                ...($student->firstgen ? ['First Gen'] : []),
                ...($student->dual ? ['Dual'] : []),
                ...($student->sap ? ['SAP'] : []),
                ...(filled($student->dfw) ? ["DFW {$student->dfw->format('m/d/Y')}"] : []),
            ],
            'breadcrumbs' => $this->getBreadcrumbs(),
            'details' => [
                ['Student', 'heroicon-m-user'],
                ...(filled($student->preferred) ? [["Goes by \"{$student->preferred}\"", 'heroicon-m-heart']] : []),
                ...(filled($student->phone) ? [[$student->phone, 'heroicon-m-phone']] : []),
                ...(filled($student->email) ? [[$student->email, 'heroicon-m-envelope']] : []),
                ...(filled($student->hsgrad) ? [[$student->hsgrad, 'heroicon-m-building-library']] : []),
            ],
            'hasSisSystem' => $sisSettings->is_enabled && $sisSettings->sis_system,
            'educatable' => $student,
            'educatableInitials' => str($studentName)
                ->trim()
                ->explode(' ')
                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                ->join(' '),
            'educatableName' => $studentName,
            'timezone' => app(DisplaySettings::class)->getTimezone(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            SyncStudentSisAction::make(),
            SubscribeHeaderAction::make(),
        ];
    }
}
