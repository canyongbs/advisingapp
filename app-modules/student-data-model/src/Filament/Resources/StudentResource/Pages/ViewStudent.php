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
use Filament\Infolists\Infolist;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Support\Facades\FilamentView;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Infolists\Components\StudentHeaderSection;
use AdvisingApp\Notification\Filament\Actions\SubscribeHeaderAction;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use App\Infolists\Components\StudentProfileInformation;
use Filament\Infolists\Components\Split;
use Illuminate\Support\Str;

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
                fn(): View => view('student-data-model::filament.resources.student-resource.sis-sync', [
                    'student' => $this->getRecord(),
                ]),
                scopes: ViewStudent::class,
            );
        }
    }

    public function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/view-record.title');
    }

    // public function getTitle(): string
    // {
    //     return false; // or return false;
    // }

    // public function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             StudentHeaderSection::make()
    //                 ->state($this->getRecord())->columnSpanFull(),
    //             StudentProfileInformation::make('Profile Information')
    //                 ->state($this->getRecord())->columnSpanFull(),
    //             // Split::make([
    //             //     Section::make('Profile Information')
    //             //         ->schema([
    //             //             TextEntry::make('sisid')
    //             //                 ->label('Student ID'),
    //             //             TextEntry::make('otherid')
    //             //                 ->label('Other ID'),
    //             //             TextEntry::make('f_e_term')
    //             //                 ->label('First Enrollment Term')
    //             //                 ->default('N/A'),
    //             //             TextEntry::make('mr_e_term')
    //             //                 ->label('Most Recent Enrollment Term')
    //             //                 ->default('N/A'),
    //             //         ]),
    //             //     Section::make('Profile Information')
    //             //         ->schema([
    //             //             TextEntry::make('sisid')
    //             //                 ->label('Student ID'),
    //             //             TextEntry::make('otherid')
    //             //                 ->label('Other ID'),
    //             //             TextEntry::make('f_e_term')
    //             //                 ->label('First Enrollment Term')
    //             //                 ->default('N/A'),
    //             //             TextEntry::make('mr_e_term')
    //             //                 ->label('Most Recent Enrollment Term')
    //             //                 ->default('N/A'),
    //             //         ]),
    //             // ])->from('md')
    //             // Section::make('Characteristics')
    //             //     ->schema([
    //             //         TextEntry::make('sisid')
    //             //             ->label('Student ID'),
    //             //         TextEntry::make('otherid')
    //             //             ->label('Other ID'),
    //             //         TextEntry::make('f_e_term')
    //             //             ->label('First Enrollment Term')
    //             //             ->default('N/A'),
    //             //         TextEntry::make('mr_e_term')
    //             //             ->label('Most Recent Enrollment Term')
    //             //             ->default('N/A'),
    //             //     ])
    //             //     ->columns(2),
    //             // Section::make('Demographics')
    //             //     ->schema([
    //             //         TextEntry::make('first')
    //             //             ->label('First Name'),
    //             //         TextEntry::make('last')
    //             //             ->label('Last Name'),
    //             //         TextEntry::make('full_name')
    //             //             ->label('Full Name'),
    //             //         TextEntry::make('preferred')
    //             //             ->label('Preferred Name')
    //             //             ->default('N/A'),
    //             //         TextEntry::make('birthdate'),
    //             //         TextEntry::make('hsgrad')
    //             //             ->label('High School Graduation')
    //             //             ->default('N/A'),
    //             //         IconEntry::make('firstgen')
    //             //             ->label('First Generation')
    //             //             ->boolean(),
    //             //         TextEntry::make('ethnicity'),
    //             //         IconEntry::make('dual')
    //             //             ->label('Dual')
    //             //             ->boolean(),
    //             //     ])
    //             //     ->columns(2),
    //             // Section::make('Contact Information')
    //             //     ->schema([
    //             //         TextEntry::make('email')
    //             //             ->label('Email Address'),
    //             //         TextEntry::make('email_2')
    //             //             ->label('Alternate Email')
    //             //             ->default('N/A'),
    //             //         TextEntry::make('mobile'),
    //             //         TextEntry::make('phone'),
    //             //         TextEntry::make('address'),
    //             //         TextEntry::make('address2')
    //             //             ->label('Apartment/Unit Number')
    //             //             ->default('N/A'),
    //             //         TextEntry::make('address3')
    //             //             ->label('Additional Address')
    //             //             ->default('N/A'),
    //             //         TextEntry::make('city'),
    //             //         TextEntry::make('state'),
    //             //         TextEntry::make('postal'),
    //             //     ])
    //             //     ->columns(2),
    //             // Section::make('Engagement Restrictions')
    //             //     ->schema([
    //             //         IconEntry::make('sms_opt_out')
    //             //             ->label('SMS Opt Out')
    //             //             ->boolean(),
    //             //         IconEntry::make('email_bounce')
    //             //             ->label('Email Bounce')
    //             //             ->boolean(),
    //             //         IconEntry::make('ferpa')
    //             //             ->label('FERPA')
    //             //             ->boolean(),
    //             //     ])
    //             //     ->columns(2),
    //             // Section::make('Impediments')
    //             //     ->schema([
    //             //         TextEntry::make('dfw')
    //             //             ->label('DFW'),
    //             //         IconEntry::make('sap')
    //             //             ->label('SAP')
    //             //             ->boolean(),
    //             //         TextEntry::make('holds'),
    //             //     ])
    //             //     ->columns(2),
    //         ]);
    // }

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

    public function getNameWords(): string
    {
        return collect(Str::of($this->record?->full_name)->explode(' '))
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

    // public function getRecordTitle(?Model $record): string | Htmlable | null
    // {
    //     return '';
    // }
}
