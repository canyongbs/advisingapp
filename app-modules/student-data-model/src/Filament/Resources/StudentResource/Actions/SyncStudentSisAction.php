<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Actions;

use Throwable;
use App\Models\Tenant;
use App\Services\Olympus;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;

class SyncStudentSisAction
{
    public static function make(): Action
    {
        $sisSettings = app(StudentInformationSystemSettings::class);

        return Action::make('refreshSis')
            ->label('Sync')
            ->labeledFrom('sm')
            ->icon('heroicon-m-arrow-path')
            ->color('gray')
            ->action(function (Student $student) {
                $tenantId = Tenant::current()->getKey();

                try {
                    $response = app(Olympus::class)->makeRequest()
                        ->asJson()
                        ->post("integrations/{$tenantId}/student-on-demand-sync", [
                            'sisid' => $student->getKey(),
                            'otherid' => $student->otherid,
                        ])
                        ->throw();

                    if ($response->ok()) {
                        Notification::make()
                            ->title('Student data sync initiated!')
                            ->body('The student data sync has been initiated. Please allow some time for the data to be updated.')
                            ->success()
                            ->send();
                    }

                    return;
                } catch (Throwable $e) {
                    report($e);
                }

                Notification::make()
                    ->title('Failed to initiate Student data sync.')
                    ->danger()
                    ->send();
            })
            ->visible($sisSettings->is_enabled && $sisSettings->sis_system);
    }
}
