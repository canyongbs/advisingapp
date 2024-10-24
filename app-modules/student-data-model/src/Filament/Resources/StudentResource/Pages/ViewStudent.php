<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Throwable;
use App\Models\Tenant;
use App\Services\Olympus;
use Illuminate\Support\Str;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Filament\Actions\SubscribeHeaderAction;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static string $view = 'student-data-model::filament.resources.student-resource.view-student';

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    protected static string $layout = 'filament-panels::components.layout.index';

    public function boot()
    {
        $sisSettings = app(StudentInformationSystemSettings::class);

        if (
            $sisSettings->is_enabled
            && ! empty($sisSettings->sis_system)
        ) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE,
                fn (): View => view('student-data-model::filament.resources.student-resource.sis-sync', [
                    'student' => $this->getRecord(),
                ]),
                scopes: ViewStudent::class,
            );
        }
    }

    public function sisRefresh()
    {
        $tenantId = Tenant::current()->getKey();

        /** @var Student $student */
        $student = $this->getRecord();

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
    }

    public function getAbbreviatedName(): string
    {
        $name = $this->record?->full_name;

        if (empty($name)) {
            $name = $this->record?->first . ' ' . $this->record?->last;
        }

        return collect(Str::of($name)->explode(' '))
            ->map(function ($word) {
                return Str::substr($word, 0, 1);
            })->implode('');
    }

    protected function getHeaderActions(): array
    {
        return [
            SubscribeHeaderAction::make(),
        ];
    }
}
