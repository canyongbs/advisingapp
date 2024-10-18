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

namespace AdvisingApp\StudentDataModel\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use App\Features\ManageStudentConfigurationFeature;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageEnrollmentResource\Pages\ListManageEnrollments;

class ManageEnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $label = 'Enrollments';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ManageStudentConfigurationFeature::active()
            && $user->can('student_record_manager.view-any')
            && app(ManageStudentConfigurationSettings::class)->is_enabled;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sisid')
                    ->label('Student ID')
                    ->string()
                    ->maxLength(255)
                    ->required(),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->label('Division'),
                TextInput::make('class_nbr')
                    ->label('Class NBR')
                    ->string()
                    ->maxLength(255),
                TextInput::make('crse_grade_off')
                    ->string()
                    ->maxLength(255)
                    ->label('CRSE grade off'),
                TextInput::make('unt_taken')
                    ->label('UNT taken')
                    ->numeric(),
                TextInput::make('unt_earned')
                    ->label('UNT earned')
                    ->numeric(),
                DateTimePicker::make('last_upd_dt_stmp')
                    ->label('Last UPD date STMP')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                TextInput::make('section')
                    ->label('Section')
                    ->string()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('department')
                    ->label('Department')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_name')
                    ->label('Faculty name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_email')
                    ->label('Faculty email')
                    ->email(),
                TextInput::make('semester_code')
                    ->label('Semester code')
                    ->string()
                    ->maxLength(255),
                TextInput::make('semester_name')
                    ->label('Semester name')
                    ->string()
                    ->maxLength(255),
                DateTimePicker::make('start_date')
                    ->label('Start date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('end_date')
                    ->label('End date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManageEnrollments::route('/'),
        ];
    }
}
