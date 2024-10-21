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
use AdvisingApp\StudentDataModel\Models\Program;
use App\Filament\Clusters\ConstituentManagement;
use App\Features\ManageStudentConfigurationFeature;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource\Pages\ListManagePrograms;

class ManageProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $label = 'Programs';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ManageStudentConfigurationFeature::active() && $user->can('student_record_manager.view-any') && app(ManageStudentConfigurationSettings::class)->is_enabled;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sisid')
                    ->label('Student ID')
                    ->required()
                    ->string()
                    ->maxLength(255),
                TextInput::make('otherid')
                    ->label('Other ID')
                    ->required()
                    ->string()
                    ->maxLength(255),
                TextInput::make('acad_career')
                    ->string()
                    ->maxLength(255)
                    ->required()
                    ->label('ACAD career'),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->required()
                    ->label('Division'),
                TextInput::make('acad_plan')
                    ->required()
                    ->label('ACAD plan'),
                TextInput::make('prog_status')
                    ->required()
                    ->label('PROG status')
                    ->default('AC'),
                TextInput::make('cum_gpa')
                    ->required()
                    ->label('Cum GPA')
                    ->numeric(),
                TextInput::make('semester')
                    ->required()
                    ->label('Semester')
                    ->string()
                    ->maxLength(255),
                TextInput::make('descr')
                    ->required()
                    ->label('DESCR')
                    ->string()
                    ->maxLength(255),
                TextInput::make('foi')
                    ->required()
                    ->label('Field of interest'),
                DateTimePicker::make('change_dt')
                    ->required()
                    ->label('Change date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('declare_dt')
                    ->required()
                    ->label('Declare date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManagePrograms::route('/'),
        ];
    }
}
